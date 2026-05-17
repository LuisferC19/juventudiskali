<?php
/**
 * config/app.php
 *
 * MEJORA: Se agrega APP_DEBUG para controlar el nivel de detalle
 * en los mensajes de error (usada en Database.php).
 * En producción establece APP_DEBUG en false.
 */
define('APP_NAME',    'Juventud Iskali');
define('APP_VERSION', '1.0');
define('APP_DESC',    'Plataforma de gestión social');
define('APP_CIUDAD',  'Tlaxcala / Puebla');
define('APP_ANIO',    '2026');
define('ADMIN_NOMBRE','Administrador Iskali');
define('ADMIN_ROL',   'Administrador');

// ✅ BASE_URL debe coincidir con el nombre de la carpeta en www/
define('BASE_URL', '/juventudiskali');

// ✅ Modo debug: true en desarrollo, false en producción
define('APP_DEBUG', true);

date_default_timezone_set('America/Mexico_City');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>