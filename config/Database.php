<?php
/**
 * config/Database.php — Conexión PDO a MySQL
 *
 * MEJORAS:
 * - En producción ya no expone el mensaje técnico del PDOException al navegador.
 *   En su lugar lo registra en el log y muestra un mensaje genérico amigable.
 * - Se detecta el entorno mediante la constante APP_DEBUG (definida en app.php).
 */
class Database
{
    private ?PDO $pdo = null;

    public function __construct()
    {
        $envPath = dirname(__DIR__) . '/env.php';

        if (!file_exists($envPath)) {
            // Este mensaje sí puede mostrarse porque no expone datos sensibles
            die('Error de configuración: no se encontró el archivo env.php.');
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
            // Registrar el error real en el log (nunca al usuario)
            logger('ERROR DB: ' . $e->getMessage());

            // En desarrollo (APP_DEBUG = true) mostrar detalle; en producción, mensaje genérico
            if (defined('APP_DEBUG') && APP_DEBUG === true) {
                die('Error de conexión (modo debug): ' . $e->getMessage());
            }

            die('No se pudo conectar a la base de datos. Por favor intenta más tarde.');
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}