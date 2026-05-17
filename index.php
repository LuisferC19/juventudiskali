<?php
/**
 * index.php — Router principal del sistema Iskali
 *
 * CORRECCIONES:
 * 1. case 'voluntarios': ahora usa VoluntariosController (existía el archivo
 *    pero la ruta seguía apuntando al DashboardController).
 * 2. case 'planning': el bloque match() fue reemplazado por if/elseif claros.
 *    El match anterior enviaba 'crear' y 'actualizar_estado' a $ctrl->index(),
 *    ignorando que esas acciones son POST-AJAX manejadas dentro del propio
 *    PlanningController::index(). Ahora el router simplemente llama a index()
 *    siempre y deja que el controlador resuelva la acción correcta.
 */

require_once 'config/app.php';
require_once 'functions.php';
require_once 'config/Database.php';

$db       = new Database();
$conexion = $db->getConnection();

$pagina = $_GET['pagina'] ?? 'inicio';

switch ($pagina) {

    case 'login':
        require_once 'controllers/AuthController.php';
        (new AuthController($conexion))->login();
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        (new AuthController($conexion))->logout();
        break;

    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        (new DashboardController($conexion))->index();
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
        (new DashboardController($conexion))->donaciones();
        break;

    case 'beneficiarios':
        require_once 'controllers/BeneficiariosController.php';
        (new BeneficiariosController($conexion))->index();
        break;

    case 'entregas':
        require_once 'controllers/DashboardController.php';
        (new DashboardController($conexion))->entregas();
        break;

    case 'inventario':
        require_once 'controllers/DashboardController.php';
        (new DashboardController($conexion))->inventario();
        break;

    // ── VOLUNTARIOS ───────────────────────────────────────────────────────
    // CORRECCIÓN: antes llamaba a DashboardController->voluntarios().
    // Ahora usa el controlador dedicado que ya existía en el proyecto.
    case 'voluntarios':
        require_once 'controllers/VoluntariosController.php';
        (new VoluntariosController($conexion))->index();
        break;

    case 'gamificacion':
        require_once 'controllers/DashboardController.php';
        (new DashboardController($conexion))->gamificacion();
        break;

    // ── RESPALDOS ─────────────────────────────────────────────────────────
    case 'respaldos':
        require_once 'controllers/BackupController.php';
        $controller = new BackupController($conexion);
        $accion     = $_GET['accion'] ?? 'index';

        if ($accion === 'generar') {
            $controller->generar();
        } elseif ($accion === 'importar') {
            $controller->importar();
        } else {
            $controller->index();
        }
        break;

    // ── REPORTES ──────────────────────────────────────────────────────────
    case 'reportes':
        require_once 'controllers/ReportesController.php';
        $controller = new ReportesController($conexion);
        $accion     = $_GET['accion'] ?? 'index';

        if ($accion === 'generar') {
            $controller->generar();
        } else {
            $controller->index();
        }
        break;

    case 'notificaciones':
        require_once 'controllers/NotificacionesController.php';
        $ctrl = new NotificacionesController($conexion);
        $ctrl->index();
        break;

    // ── PLANNING ──────────────────────────────────────────────────────────
    // CORRECCIÓN: el match() anterior era confuso y enviaba 'crear' y
    // 'actualizar_estado' a index() de todas formas. Se simplificó:
    // el router siempre llama a index() y PlanningController resuelve
    // internamente cada acción (GET/POST, AJAX vs vista).
    case 'planning':
        require_once 'controllers/PlanningController.php';
        $ctrl   = new PlanningController($conexion);
        $accion = $_GET['accion'] ?? 'index';

        if ($accion === 'actividades_mes') {
            $ctrl->getActividadesMes();   // único método público AJAX del controlador
        } else {
            $ctrl->index();               // index() maneja crear, actualizar_estado y la vista
        }
        break;

    case 'home':
    case 'inicio':
        require_once 'controllers/InicioController.php';
        (new InicioController())->index();
        break;

    default:
        require_once 'controllers/InicioController.php';
        (new InicioController())->index();
        break;
}