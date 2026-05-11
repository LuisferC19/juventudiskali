<?php
/**
 * models/BackupModel.php
 * Modelo para el módulo de respaldos de base de datos.
 * Contiene configuración necesaria para mysqldump.
 */
class BackupModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Obtiene la configuración de la base de datos desde env.php
     */
    public function getDatabaseConfig(): array
    {
        $envPath = dirname(__DIR__) . '/env.php';
        if (!file_exists($envPath)) {
            throw new Exception("No se encontró env.php en: " . $envPath);
        }
        return require $envPath;
    }

    /**
     * Genera el comando mysqldump para Windows.
     * Nota: Ajusta la ruta de mysqldump según tu instalación de Laragon.
     */
    public function getMysqldumpCommand(string $outputFile): string
    {
        $config = $this->getDatabaseConfig();

        // Ruta de mysqldump en Laragon
        $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

        // Si no existe, intenta una ruta genérica
        if (!file_exists($mysqldumpPath)) {
            $mysqldumpPath = 'mysqldump'; // Asume que está en PATH
        }

        // Comando con parámetros seguros
        $command = escapeshellarg($mysqldumpPath) . ' ' .
                   '--host=' . escapeshellarg($config['DB_HOST']) . ' ' .
                   '--user=' . escapeshellarg($config['DB_USER']) . ' ' .
                   '--password=' . escapeshellarg($config['DB_PASS']) . ' ' .
                   '--default-character-set=' . escapeshellarg($config['DB_CHARSET']) . ' ' .
                   '--single-transaction ' .
                   '--routines ' .
                   '--triggers ' .
                   escapeshellarg($config['DB_NAME']) . ' > ' . escapeshellarg($outputFile);

        return $command;
    }

    /**
     * Verifica si el directorio de respaldos existe, si no, lo crea.
     */
    public function ensureBackupDirectory(): string
    {
        $backupDir = dirname(__DIR__) . '/Respaldos';
        if (!is_dir($backupDir)) {
            if (!mkdir($backupDir, 0755, true)) {
                throw new Exception("No se pudo crear el directorio de respaldos: " . $backupDir);
            }
        }
        return $backupDir;
    }
}