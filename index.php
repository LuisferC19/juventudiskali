<?php
/**
 * index.php — Router principal del sistema Iskali
 * Maneja todas las peticiones y redirige al controlador correcto.
 */

// Cargar configuración global
require_once 'config/app.php';
require_once 'functions.php';
require_once 'config/Database.php';   

// Crear conexión PDO
$db       = new Database();
$conexion = $db->getConnection();

// Obtener la página solicitada (por defecto: inicio)
$pagina = $_GET['pagina'] ?? 'inicio';

// Enrutador principal
switch ($pagina) {

    case 'login':
        require_once 'controllers/AuthController.php';
        (new AuthController($conexion))->login();   // ✅ pasa $conexion al controlador
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        (new AuthController($conexion))->logout();
        break;

    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    case 'campanas':
        require_once 'controllers/CampanasController.php';
        (new CampanasController())->index();
        break;

    case 'usuarios':
        require_once 'controllers/UsuariosController.php';
        (new UsuariosController($conexion))->index();
        break;

    case 'donadores':
        require_once 'controllers/DonadoresController.php';
        (new DonadoresController($conexion))->index();
        break;

    case 'donaciones':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->donaciones();
        break;

    case 'beneficiarios':
        require_once 'controllers/BeneficiariosController.php';
        (new BeneficiariosController($conexion))->index();
        break;

    case 'entregas':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->entregas();
        break;

    case 'inventario':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->inventario();
        break;

    case 'voluntarios':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->voluntarios();
        break;

    case 'gamificacion':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->gamificacion();
        break;

    case 'home':
    case 'inicio':
        require_once 'controllers/InicioController.php';
        (new InicioController())->index();
        break;

    default:
        // Ruta desconocida → redirigir a la landing (inicio)
        require_once 'controllers/InicioController.php';
        (new InicioController())->index();
        break;
}