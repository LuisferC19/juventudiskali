<?php
/**
 * controllers/BackupController.php
 * Controlador para el módulo de respaldos de base de datos.
 * Maneja la generación y descarga de respaldos .sql.
 */
class BackupController
{
    private PDO $db;
    private BackupModel $backupModel;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
        require_once 'models/BackupModel.php';
        $this->backupModel = new BackupModel($conexion);
    }

    /**
     * Verifica que haya sesión activa y que el usuario sea administrador.
     */
    private function verificarAccesoAdmin(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }

        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            // Mostrar vista de acceso denegado
            $pagina_activa = 'respaldos';
            $titulo_pagina = 'Respaldos - Acceso Denegado';
            $error = 'Acceso denegado. Solo administradores pueden generar respaldos.';
            require_once 'views/pages/RespaldosView.php';
            exit;
        }
    }

    /**
     * Muestra la vista principal de respaldos.
     */
    public function index(): void
    {
        $this->verificarAccesoAdmin();

        $pagina_activa = 'respaldos';
        $titulo_pagina = 'Respaldos de Base de Datos';
        $mensaje = '';
        $error = $_GET['error'] ?? '';

        require_once 'views/pages/RespaldosView.php';
    }

    /**
     * Genera y descarga el respaldo de la base de datos.
     */
    public function generar(): void
    {
        $this->verificarAccesoAdmin();

        try {
            // Asegurar que el directorio existe
            $backupDir = $this->backupModel->ensureBackupDirectory();

            // Generar nombre del archivo con fecha y hora
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_iskali_{$timestamp}.sql";
            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Obtener el comando mysqldump
            $command = $this->backupModel->getMysqldumpCommand($filepath);

            // Ejecutar el comando
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception("Error al ejecutar mysqldump. Código: {$returnCode}. Comando: {$command}");
            }

            if (!file_exists($filepath)) {
                throw new Exception("El archivo de respaldo no se generó correctamente.");
            }

            // Forzar descarga del archivo
            $this->descargarArchivo($filepath, $filename);

            // Después de la descarga, eliminar el archivo temporal
            unlink($filepath);

        } catch (Exception $e) {
            // Redirigir con error
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=respaldos&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Fuerza la descarga del archivo .sql
     */
    private function descargarArchivo(string $filepath, string $filename): void
    {
        if (!file_exists($filepath)) {
            throw new Exception("Archivo no encontrado para descarga.");
        }

        // Limpiar cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Headers para descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        // Leer y enviar el archivo
        readfile($filepath);
        exit;
    }
}