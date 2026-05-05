<?php
/**
 * config/app.php
 */
define('APP_NAME',    'Juventud Iskali');
define('APP_VERSION', '1.0');
define('APP_DESC',    'Plataforma de gestión social');
define('APP_CIUDAD',  'Tlaxcala / Puebla, México');
define('APP_ANIO',    '2026');
define('ADMIN_NOMBRE','Administrador Iskalli');
define('ADMIN_ROL',   'Administrador');

// ✅ BASE_URL debe coincidir con el nombre de la carpeta en www/
// En este proyecto la carpeta es: juventudiskali
// Por eso se usa: localhost/juventudiskali
define('BASE_URL', '/juventudiskali');

date_default_timezone_set('America/Mexico_City');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>