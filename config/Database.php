<?php
/**
 * config/Database.php — Conexión PDO a MySQL
 */
class Database
{
    private ?PDO $pdo = null;

    public function __construct()
    {
        // ✅ Ruta ABSOLUTA al env.php (antes usaba ruta relativa y fallaba)
        $envPath = dirname(__DIR__) . '/env.php';

        if (!file_exists($envPath)) {
            die("Error: No se encontró env.php en: " . $envPath);
        }

        $env = require $envPath;

        $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset={$env['DB_CHARSET']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], $options);
        } catch (PDOException $e) {
            // En desarrollo muestra el error real para depurar
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}