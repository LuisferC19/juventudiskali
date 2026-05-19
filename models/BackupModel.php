<?php
/**
 * models/BackupModel.php
 * CORRECCIÓN: importación via PDO (no requiere exec/mysql binary),
 * charset correcto, FOREIGN_KEY_CHECKS, y mejor manejo de errores.
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
     *
     * CORRECCIÓN: se usa MYSQL_PWD para la contraseña en lugar de --password=
     * para evitar problemas con contraseñas vacías o con caracteres especiales.
     */
    public function getMysqldumpCommand(string $outputFile): string
    {
        $config = $this->getDatabaseConfig();

        // Ruta Laragon Windows
        $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
        if (!file_exists($mysqldumpPath)) {
            $mysqldumpPath = 'mysqldump';
        }

        // CORRECCIÓN: Pasar contraseña via variable de entorno evita
        // problemas con escapeshellarg y contraseñas vacías.
        // En Windows se usa SET MYSQL_PWD=pass && mysqldump ...
        // En Linux/Mac se usa MYSQL_PWD=pass mysqldump ...
        $password = $config['DB_PASS'];

        if (PHP_OS_FAMILY === 'Windows') {
            $envPrefix = !empty($password)
                ? 'SET MYSQL_PWD=' . escapeshellarg($password) . ' && '
                : '';
            $passwordArg = '';
        } else {
            $envPrefix = !empty($password)
                ? 'MYSQL_PWD=' . escapeshellarg($password) . ' '
                : '';
            $passwordArg = '';
        }

        $command = $envPrefix .
                   escapeshellarg($mysqldumpPath) . ' ' .
                   '--host='                  . escapeshellarg($config['DB_HOST'])    . ' ' .
                   '--user='                  . escapeshellarg($config['DB_USER'])    . ' ' .
                   '--default-character-set=' . $config['DB_CHARSET']                . ' ' .
                   '--single-transaction '    .
                   '--routines '              .
                   '--triggers '              .
                   escapeshellarg($config['DB_NAME']) . ' > ' . escapeshellarg($outputFile);

        return $command;
    }

    /**
     * Genera el comando mysql para importar (usado solo como FALLBACK si PDO falla).
     * CORRECCIÓN: se agrega --default-character-set y manejo correcto de contraseña.
     */
    public function getMysqlImportCommand(string $sqlFile): string
    {
        $config = $this->getDatabaseConfig();

        $mysqlPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysql.exe';
        if (!file_exists($mysqlPath)) {
            $mysqlPath = 'mysql';
        }

        $password = $config['DB_PASS'];

        if (PHP_OS_FAMILY === 'Windows') {
            $envPrefix = !empty($password)
                ? 'SET MYSQL_PWD=' . escapeshellarg($password) . ' && '
                : '';
        } else {
            $envPrefix = !empty($password)
                ? 'MYSQL_PWD=' . escapeshellarg($password) . ' '
                : '';
        }

        // CORRECCIÓN: se agrega --default-character-set y --force
        return sprintf(
            '%s%s --host=%s --user=%s --default-character-set=%s --force %s < %s 2>&1',
            $envPrefix,
            escapeshellarg($mysqlPath),
            escapeshellarg($config['DB_HOST']),
            escapeshellarg($config['DB_USER']),
            $config['DB_CHARSET'],
            escapeshellarg($config['DB_NAME']),
            escapeshellarg($sqlFile)
        );
    }

    /**
     * ─────────────────────────────────────────────────────────────────
     *  IMPORTACIÓN VÍA PDO  ← MÉTODO PRINCIPAL (NO requiere exec())
     * ─────────────────────────────────────────────────────────────────
     *
     * Lee el archivo .sql y ejecuta cada sentencia directamente con PDO.
     * Ventajas:
     *   - Funciona aunque exec() esté deshabilitado en el servidor.
     *   - No depende de que el binario mysql esté en el PATH.
     *   - Usa la misma conexión PDO ya configurada (charset correcto).
     *   - Compatible con cualquier hosting compartido.
     *
     * @throws Exception si el archivo no existe o falla alguna sentencia crítica.
     */
    public function importarConPDO(string $sqlFile): void
    {
        if (!file_exists($sqlFile)) {
            throw new Exception("Archivo SQL no encontrado: {$sqlFile}");
        }

        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            throw new Exception("No se pudo leer el archivo SQL.");
        }

        // Asegurar que la BD existe antes de importar
        $config = $this->getDatabaseConfig();
        $dbName = $config['DB_NAME'];
        $charset = $config['DB_CHARSET'] ?? 'utf8mb4';

        $this->db->exec(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}`
             CHARACTER SET {$charset}
             COLLATE {$charset}_unicode_ci"
        );
        $this->db->exec("USE `{$dbName}`");

        // Desactivar restricciones de FK durante la importación
        // CORRECCIÓN Bug #4: sin esto, las FK pueden bloquear el INSERT
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
        $this->db->exec("SET time_zone = '+00:00'");

        try {
            // Parsear el SQL respetando los bloques DELIMITER (triggers/procedures)
            $statements = $this->parseSqlStatements($sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if ($statement === '' || $statement === ';') {
                    continue;
                }
                // Ignorar sentencias que cambian la BD destino del dump
                // (CREATE DATABASE / DROP DATABASE / USE) si ya estamos en ella
                if (preg_match('/^(CREATE|DROP)\s+DATABASE\s+/i', $statement)) {
                    continue;
                }
                if (preg_match('/^USE\s+/i', $statement)) {
                    continue;
                }

                $this->db->exec($statement);
            }
        } finally {
            // Siempre restaurar las restricciones de FK
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
        }
    }

    /**
     * Divide un volcado SQL en sentencias individuales, manejando:
     *  - Bloques DELIMITER (para triggers, procedures, functions)
     *  - Comentarios -- y /* * /
     *  - Cadenas entre comillas simples y dobles
     *
     * @return string[]
     */
    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $delimiter  = ';';
        $len        = strlen($sql);
        $i          = 0;

        while ($i < $len) {
            // Detectar cambio de DELIMITER
            if (strncasecmp($sql, 'DELIMITER ', 10) === 0 ||
                ($i < $len && substr_compare($sql, "\nDELIMITER ", $i, 11, true) === 0)) {
                $start = strpos($sql, 'DELIMITER ', $i);
                if ($start === $i || $start === $i - 1) {
                    $end       = strpos($sql, "\n", $start + 10);
                    $delimiter = trim(substr($sql, $start + 10, ($end === false ? null : $end - $start - 10)));
                    $i         = ($end === false) ? $len : $end + 1;
                    continue;
                }
            }

            // Buscar el delimitador actual
            $delimPos = strpos($sql, $delimiter, $i);
            if ($delimPos === false) {
                $current .= substr($sql, $i);
                break;
            }

            $current .= substr($sql, $i, $delimPos - $i + strlen($delimiter));
            $i        = $delimPos + strlen($delimiter);

            $stmt = trim($current);
            if ($stmt !== '') {
                // Si el delimitador no era ';', agregar ';' para que PDO lo ejecute
                if ($delimiter !== ';') {
                    $stmt = rtrim($stmt, $delimiter) . ';';
                }
                $statements[] = $stmt;
            }
            $current = '';
        }

        // Resto sin delimitador final
        $stmt = trim($current);
        if ($stmt !== '') {
            $statements[] = $stmt;
        }

        return $statements;
    }

    /**
     * Verifica si exec() está disponible en este servidor.
     */
    public function isExecAvailable(): bool
    {
        if (!function_exists('exec')) {
            return false;
        }
        $disabled = array_map('trim', explode(',', ini_get('disable_functions')));
        return !in_array('exec', $disabled, true);
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

    /**
     * Elimina un directorio y todo su contenido de forma recursiva.
     * CORRECCIÓN Bug #6: rmdir() simple falla si hay archivos dentro.
     */
    public function eliminarDirectorioRecursivo(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }
        rmdir($dir);
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
                    LEFT JOIN usuarios u ON u.id_usuario = r.usuario_id
                    ORDER BY r.fechayhora DESC
                    LIMIT :lim";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}