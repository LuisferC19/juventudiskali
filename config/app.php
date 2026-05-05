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

// ✅ BASE_URL = nombre exacto de tu carpeta en www/
// Tu carpeta se llama: juvetud_iskali.io
// Por eso accedes en: localhost:8080/juvetud_iskali.io/
define('BASE_URL', '/juvetud_iskali.io');

date_default_timezone_set('America/Mexico_City');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>