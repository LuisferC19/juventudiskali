<?php
/**
 * controllers/BackupController.php
 * VERSION MEJORADA: exportación ZIP, importación ZIP, historial en BD.
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
                throw new Exception("Error al ejecutar mysqldump. Código: {$returnCode}");
            }
            if (!file_exists($sqlFile)) {
                throw new Exception("El archivo SQL no se generó correctamente.");
            }

            // 2. Comprimir el .sql en un .zip
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                throw new Exception("No se pudo crear el archivo ZIP.");
            }
            $zip->addFile($sqlFile, basename($sqlFile));
            $zip->close();
            unlink($sqlFile); // eliminar el .sql temporal

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

            // 4. Enviar el ZIP al navegador y eliminarlo después de la descarga
            $this->descargarArchivo($zipFile, $filename, true);

        } catch (Exception $e) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    // ─────────────────────────────────────────────
    //  IMPORTAR – restaura la BD desde un .zip
    // ─────────────────────────────────────────────

    public function importar(): void
    {
        $this->verificarAccesoAdmin();

        // Validar que llegó un archivo por POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['backup_file'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode('No se recibió ningún archivo.'));
            exit;
        }

        $file = $_FILES['backup_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode('Error al subir el archivo. Código PHP: ' . $file['error']));
            exit;
        }

        // Solo aceptar .zip
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode('Solo se aceptan archivos .zip'));
            exit;
        }

        try {
            $backupDir = $this->backupModel->ensureBackupDirectory();
            $tempDir   = $backupDir . DIRECTORY_SEPARATOR . 'temp_import' . DIRECTORY_SEPARATOR;

            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipDestino = $tempDir . basename($file['name']);

            if (!move_uploaded_file($file['tmp_name'], $zipDestino)) {
                throw new Exception("No se pudo mover el archivo ZIP al servidor.");
            }

            // Extraer el .sql del .zip
            $zip     = new ZipArchive();
            $sqlFile = null;

            if ($zip->open($zipDestino) !== true) {
                throw new Exception("No se pudo abrir el archivo ZIP.");
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
                unlink($zipDestino);
                throw new Exception("El ZIP no contiene ningún archivo .sql válido.");
            }

            $zip->extractTo($tempDir, $sqlFile);
            $zip->close();

            $rutaSql = $tempDir . $sqlFile;

            // Importar a MySQL
            $comando    = $this->backupModel->getMysqlImportCommand($rutaSql);
            $output     = [];
            $returnCode = 0;
            exec($comando, $output, $returnCode);

            // Limpieza de temporales
            if (file_exists($rutaSql))  { unlink($rutaSql); }
            if (file_exists($zipDestino)){ unlink($zipDestino); }
            if (is_dir($tempDir))        { @rmdir($tempDir); }

            if ($returnCode !== 0) {
                throw new Exception("Error al importar el SQL. Detalle: " . implode(' ', $output));
            }

            // Registrar en historial
            $usuarioId = $_SESSION['id_usuario'] ?? null;
            $this->backupModel->registrarOperacion(
                'IMPORTACION',
                basename($file['name']),
                'ZIP',
                $file['size'],
                $usuarioId,
                'Restauración manual desde el panel de administración.'
            );

            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&exito=importacion');
            exit;

        } catch (Exception $e) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode($e->getMessage()));
            exit;
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
