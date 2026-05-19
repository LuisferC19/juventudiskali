<?php
/**
 * controllers/BackupController.php
 * CORRECCIÓN: importación usa PDO como método principal (no requiere exec).
 * exec/mysqldump se mantiene para exportar, con mejor manejo de errores.
 */
class BackupController
{
    private PDO         $db;
    private BackupModel $backupModel;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
        require_once 'models/BackupModel.php';
        $this->backupModel = new BackupModel($conexion);
    }

    // ─────────────────────────────────────────────
    //  Seguridad
    // ─────────────────────────────────────────────

    private function verificarAccesoAdmin(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }

        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            $pagina_activa = 'respaldos';
            $titulo_pagina = 'Respaldos - Acceso Denegado';
            $error         = 'Acceso denegado. Solo administradores pueden gestionar respaldos.';
            $historial     = [];
            require_once 'views/pages/RespaldosView.php';
            exit;
        }
    }

    // ─────────────────────────────────────────────
    //  INDEX  – muestra la vista con historial
    // ─────────────────────────────────────────────

    public function index(): void
    {
        $this->verificarAccesoAdmin();

        $pagina_activa = 'respaldos';
        $titulo_pagina = 'Respaldos de Base de Datos';
        $error         = $_GET['error']   ?? '';
        $exito         = $_GET['exito']   ?? '';
        $historial     = $this->backupModel->obtenerHistorial(50);

        require_once 'views/pages/RespaldosView.php';
    }

    // ─────────────────────────────────────────────
    //  GENERAR – exporta la BD como .zip con .sql dentro
    // ─────────────────────────────────────────────

    public function generar(): void
    {
        $this->verificarAccesoAdmin();

        // CORRECCIÓN Bug #2: verificar exec() antes de intentar usar mysqldump
        if (!$this->backupModel->isExecAvailable()) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' .
                urlencode('La función exec() está deshabilitada en este servidor. Contacta a tu proveedor de hosting para habilitar mysqldump.'));
            exit;
        }

        try {
            $backupDir = $this->backupModel->ensureBackupDirectory();
            $timestamp = date('Ymd_His');
            $sqlFile   = $backupDir . DIRECTORY_SEPARATOR . "respaldo_{$timestamp}.sql";
            $zipFile   = $backupDir . DIRECTORY_SEPARATOR . "respaldo_{$timestamp}.zip";

            // 1. Ejecutar mysqldump
            $command    = $this->backupModel->getMysqldumpCommand($sqlFile);
            $output     = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $detalle = implode(' ', $output);
                throw new Exception("Error al ejecutar mysqldump (código {$returnCode}). Detalle: {$detalle}");
            }
            if (!file_exists($sqlFile) || filesize($sqlFile) === 0) {
                throw new Exception("El archivo SQL no se generó o está vacío. Verifica que mysqldump esté instalado y accesible.");
            }

            // 2. Comprimir el .sql en un .zip
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                throw new Exception("No se pudo crear el archivo ZIP.");
            }
            $zip->addFile($sqlFile, basename($sqlFile));
            $zip->close();
            unlink($sqlFile);

            if (!file_exists($zipFile)) {
                throw new Exception("El archivo ZIP no se generó correctamente.");
            }

            $bytes    = filesize($zipFile);
            $filename = basename($zipFile);

            // 3. Registrar en historial
            $usuarioId = $_SESSION['id_usuario'] ?? null;
            $this->backupModel->registrarOperacion(
                'EXPORTACION',
                $filename,
                'ZIP',
                $bytes,
                $usuarioId,
                'Respaldo manual generado desde el panel.'
            );

            // 4. Enviar el ZIP al navegador
            $this->descargarArchivo($zipFile, $filename, true);

        } catch (Exception $e) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    // ─────────────────────────────────────────────
    //  IMPORTAR – restaura la BD desde un .zip
    //  CORRECCIÓN: usa PDO como método principal.
    //  No requiere exec() ni el binario mysql en el PATH.
    // ─────────────────────────────────────────────

    public function importar(): void
    {
        $this->verificarAccesoAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['backup_file'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' .
                urlencode('No se recibió ningún archivo.'));
            exit;
        }

        $file = $_FILES['backup_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $mensajesError = [
                UPLOAD_ERR_INI_SIZE   => 'El archivo supera el límite de upload_max_filesize en php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el límite del formulario.',
                UPLOAD_ERR_PARTIAL    => 'El archivo se subió de forma incompleta.',
                UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
                UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco.',
                UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP bloqueó la subida.',
            ];
            $msg = $mensajesError[$file['error']] ?? "Error al subir el archivo. Código PHP: {$file['error']}";
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode($msg));
            exit;
        }

        // Solo aceptar .zip
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' .
                urlencode('Solo se aceptan archivos .zip generados por este sistema.'));
            exit;
        }

        $backupDir = null;
        $tempDir   = null;
        $zipDestino = null;
        $rutaSql    = null;

        try {
            $backupDir = $this->backupModel->ensureBackupDirectory();
            $tempDir   = $backupDir . DIRECTORY_SEPARATOR . 'temp_import_' . time();

            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception("No se pudo crear el directorio temporal de importación.");
            }

            $zipDestino = $tempDir . DIRECTORY_SEPARATOR . basename($file['name']);

            if (!move_uploaded_file($file['tmp_name'], $zipDestino)) {
                throw new Exception("No se pudo mover el archivo ZIP al servidor.");
            }

            // Extraer el .sql del .zip
            $zip     = new ZipArchive();
            $sqlFile = null;

            if ($zip->open($zipDestino) !== true) {
                throw new Exception("No se pudo abrir el archivo ZIP. ¿Está dañado?");
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $info = $zip->statIndex($i);
                if (strtolower(pathinfo($info['name'], PATHINFO_EXTENSION)) === 'sql') {
                    $sqlFile = $info['name'];
                    break;
                }
            }

            if (!$sqlFile) {
                $zip->close();
                throw new Exception("El ZIP no contiene ningún archivo .sql válido. ¿Es un respaldo generado por este sistema?");
            }

            $zip->extractTo($tempDir, $sqlFile);
            $zip->close();

            // CORRECCIÓN: construir la ruta correctamente manejando posibles subdirectorios en el ZIP
            $rutaSql = $tempDir . DIRECTORY_SEPARATOR . $sqlFile;

            if (!file_exists($rutaSql) || filesize($rutaSql) === 0) {
                throw new Exception("El archivo SQL extraído está vacío o no se encontró en: {$rutaSql}");
            }

            // ── Importar: PDO primero, exec como fallback ──────────────
            $metodo = 'PDO';
            try {
                $this->backupModel->importarConPDO($rutaSql);
            } catch (Exception $pdoEx) {
                // Fallback a exec/mysql si PDO falla Y exec está disponible
                if ($this->backupModel->isExecAvailable()) {
                    $metodo  = 'exec/mysql';
                    $comando = $this->backupModel->getMysqlImportCommand($rutaSql);
                    $output  = [];
                    $retCode = 0;
                    exec($comando, $output, $retCode);

                    if ($retCode !== 0) {
                        throw new Exception(
                            "PDO falló: {$pdoEx->getMessage()} | " .
                            "mysql CLI también falló: " . implode(' ', $output)
                        );
                    }
                } else {
                    // Solo PDO disponible y falló
                    throw new Exception("Error al importar via PDO: " . $pdoEx->getMessage());
                }
            }

            // Registrar en historial
            $usuarioId = $_SESSION['id_usuario'] ?? null;
            $this->backupModel->registrarOperacion(
                'IMPORTACION',
                basename($file['name']),
                'ZIP',
                $file['size'],
                $usuarioId,
                "Restauración manual (método: {$metodo}) desde el panel de administración."
            );

            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&exito=importacion');
            exit;

        } catch (Exception $e) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode($e->getMessage()));
            exit;
        } finally {
            // CORRECCIÓN Bug #6: limpiar el directorio temporal de forma recursiva
            if ($rutaSql && file_exists($rutaSql)) {
                @unlink($rutaSql);
            }
            if ($zipDestino && file_exists($zipDestino)) {
                @unlink($zipDestino);
            }
            if ($tempDir && is_dir($tempDir)) {
                $this->backupModel->eliminarDirectorioRecursivo($tempDir);
            }
        }
    }

    // ─────────────────────────────────────────────
    //  Helper – fuerza la descarga del archivo
    // ─────────────────────────────────────────────

    private function descargarArchivo(string $filepath, string $filename, bool $deleteAfter = false): void
    {
        if (!file_exists($filepath)) {
            throw new Exception("Archivo no encontrado para descarga.");
        }

        if (ob_get_level()) { ob_end_clean(); }

        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        readfile($filepath);

        if ($deleteAfter && file_exists($filepath)) {
            @unlink($filepath);
        }

        exit;
    }
}