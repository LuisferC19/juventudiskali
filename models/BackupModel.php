<?php
/**
 * models/BackupModel.php
 * VERSION MEJORADA: exportación ZIP, importación ZIP y registro de historial.
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
     * Genera el comando mysqldump.
     * Compatible con Laragon (Windows) y Linux/Mac.
     */
    public function getMysqldumpCommand(string $outputFile): string
    {
        $config = $this->getDatabaseConfig();

        // Ruta Laragon Windows
        $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
        if (!file_exists($mysqldumpPath)) {
            $mysqldumpPath = 'mysqldump'; // Asume que está en el PATH del sistema
        }

        $command = escapeshellarg($mysqldumpPath) . ' ' .
                   '--host='                 . escapeshellarg($config['DB_HOST'])    . ' ' .
                   '--user='                 . escapeshellarg($config['DB_USER'])    . ' ' .
                   '--password='             . escapeshellarg($config['DB_PASS'])    . ' ' .
                   '--default-character-set='. escapeshellarg($config['DB_CHARSET']) . ' ' .
                   '--single-transaction '  .
                   '--routines '            .
                   '--triggers '            .
                   escapeshellarg($config['DB_NAME']) . ' > ' . escapeshellarg($outputFile);

        return $command;
    }

    /**
     * Genera el comando mysql para importar un archivo .sql.
     */
    public function getMysqlImportCommand(string $sqlFile): string
    {
        $config = $this->getDatabaseConfig();

        $mysqlPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysql.exe';
        if (!file_exists($mysqlPath)) {
            $mysqlPath = 'mysql';
        }

        return sprintf(
            '%s --host=%s --user=%s --password=%s %s < %s 2>&1',
            escapeshellarg($mysqlPath),
            escapeshellarg($config['DB_HOST']),
            escapeshellarg($config['DB_USER']),
            escapeshellarg($config['DB_PASS']),
            escapeshellarg($config['DB_NAME']),
            escapeshellarg($sqlFile)
        );
    }

    /**
     * Verifica/crea el directorio de respaldos.
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

    // ─────────────────────────────────────────────
    //  HISTORIAL EN BASE DE DATOS
    // ─────────────────────────────────────────────

    /**
     * Registra una operación (exportación o importación) en la tabla respaldos.
     */
    public function registrarOperacion(
        string $tipo,
        string $nombreArchivo,
        string $formato   = 'ZIP',
        int    $bytes     = 0,
        ?int   $usuarioId = null,
        string $notas     = ''
    ): void {
        $config = $this->getDatabaseConfig();

        $sql = "INSERT INTO respaldos
                    (tipo_operacion, nombre_archivo, formato, nombre_bd, tamanio_bytes, usuario_id, observaciones)
                VALUES
                    (:tipo, :archivo, :formato, :bd, :bytes, :uid, :notas)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':tipo'    => $tipo,
            ':archivo' => $nombreArchivo,
            ':formato' => $formato,
            ':bd'      => $config['DB_NAME'],
            ':bytes'   => $bytes,
            ':uid'     => $usuarioId,
            ':notas'   => $notas,
        ]);
    }

    /**
     * Devuelve el historial de respaldos ordenado por fecha descendente.
     */
    public function obtenerHistorial(int $limite = 50): array
    {
        try {
            $sql = "SELECT r.*, u.nombre AS nombre_usuario
                    FROM respaldos r
                    LEFT JOIN usuarios u ON u.id = r.usuario_id
                    ORDER BY r.fechayhora DESC
                    LIMIT :lim";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            // Si la tabla aún no existe, retorna vacío
            return [];
        }
    }
}
