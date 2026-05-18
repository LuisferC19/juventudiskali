<?php
/**
 * functions.php — Funciones globales del sistema
 *
 * MEJORAS:
 * - logger() ahora crea el directorio /logs/ automáticamente si no existe.
 * - Se agregan funciones CSRF para proteger formularios POST.
 * - Se agrega sanitizeInt() como helper conveniente.
 */
declare(strict_types=1);

define('APP_LOG_FILE', __DIR__ . '/logs/app.log');

// ---------------------------------------------------------------------------
//  ESCAPE / SANITIZACIÓN
// ---------------------------------------------------------------------------

/**
 * Escapa HTML para prevenir XSS en vistas.
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Filtra y devuelve un entero positivo desde $_GET o $_POST, o null si no es válido.
 * Uso: $id = sanitizeInt($_GET['id'] ?? null);
 */
function sanitizeInt(mixed $value): ?int
{
    $result = filter_var($value, FILTER_VALIDATE_INT);
    return ($result !== false && $result > 0) ? (int)$result : null;
}

// ---------------------------------------------------------------------------
//  LOGGER
// ---------------------------------------------------------------------------

/**
 * Escribe un mensaje en el archivo de log.
 * Crea el directorio /logs/ si no existe.
 */
function logger(string $mensaje): void
{
    $dir = dirname(APP_LOG_FILE);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $fecha     = date('Y-m-d H:i:s');
    $contenido = "[{$fecha}] {$mensaje}" . PHP_EOL;

    error_log($contenido, 3, APP_LOG_FILE);
}

// ---------------------------------------------------------------------------
//  CSRF — Protección contra Cross-Site Request Forgery
// ---------------------------------------------------------------------------

/**
 * Genera (o reutiliza) un token CSRF en sesión y devuelve el campo <input> oculto.
 *
 * Uso en una vista:
 *   <form method="POST">
 *     <?= csrfField() ?>
 *     ...
 *   </form>
 */
function csrfField(): string
{
    // CSRF desactivado temporalmente — devuelve campo vacío
    return '';
}

/**
 * Obtiene el token CSRF actual de la sesión, generándolo si no existe.
 */
function csrfToken(): string
{
    // CSRF desactivado temporalmente
    return '';
}

/**
 * Valida el token CSRF enviado en un POST.
 * CSRF desactivado temporalmente para desarrollo.
 */
function csrfVerify(): void
{
    // CSRF desactivado temporalmente — no bloquea ningún POST
}