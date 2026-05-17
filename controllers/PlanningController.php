<?php
/**
 * controllers/PlanningController.php
 * Controlador para gestionar actividades y planning.
 */
require_once 'models/PlanningModel.php';

class PlanningController
{
    private PlanningModel $modelo;
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db     = $conexion;
        $this->modelo = new PlanningModel($conexion);
    }

    /**
     * Router principal
     */
    public function index(): void
    {
        $this->verificarSesion();

        $accion = trim($_GET['accion'] ?? '');
        $metodo = $_SERVER['REQUEST_METHOD'];

        // AJAX: Crear actividad
        if ($accion === 'crear' && $metodo === 'POST') {
            $this->crear();
            return;
        }

        // AJAX: Actualizar estado
        if ($accion === 'actualizar_estado' && $metodo === 'POST') {
            $this->actualizarEstado();
            return;
        }

        // AJAX: Obtener actividades del mes
        if ($accion === 'actividades_mes' && $metodo === 'GET') {
            $this->getActividadesMes();
            return;
        }

        // Mostrar página de planning
        $this->mostrarPlanning();
    }

    /**
     * Muestra la página de planning
     */
    private function mostrarPlanning(): void
    {
        $mes_actual = (int)($_GET['mes'] ?? date('m'));
        $anio_actual = (int)($_GET['anio'] ?? date('Y'));

        $actividades = $this->modelo->getActividades();
        $actividades_mes = $this->modelo->getActividadesPorMes($mes_actual, $anio_actual);
        $kpis = $this->modelo->getKPIs();
        $usuarios = $this->modelo->getUsuarios();

        $pagina_activa = 'planning';
        $titulo_pagina = 'Planning & Actividades';

        require_once 'views/layouts/header.php';
        require_once 'views/pages/PlanningView.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Crea una nueva actividad (AJAX)
     */
    private function crear(): void
    {
        header('Content-Type: application/json');

        // ✅ Verificar token CSRF
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSesion  = $_SESSION['csrf_token'] ?? '';
        if (empty($tokenEnviado) || empty($tokenSesion) || !hash_equals($tokenSesion, $tokenEnviado)) {
            unset($_SESSION['csrf_token']);
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF inválido.']);
            return;
        }
        unset($_SESSION['csrf_token']);

        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin = trim($_POST['fecha_fin'] ?? '');
        $zona = trim($_POST['zona'] ?? 'ambas');
        $estado = trim($_POST['estado'] ?? 'planeada');
        $id_usuario_responsable = filter_var($_POST['id_usuario_responsable'] ?? 0, FILTER_VALIDATE_INT);

        if (empty($titulo) || empty($fecha_inicio)) {
            echo json_encode(['error' => 'El título y fecha de inicio son requeridos']);
            return;
        }

        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin ?: $fecha_inicio,
            'zona' => $zona,
            'estado' => $estado,
            'id_usuario_responsable' => $id_usuario_responsable ?: null,
        ];

        if ($this->modelo->crearActividad($data)) {
            echo json_encode(['success' => true, 'message' => 'Actividad creada correctamente']);
        } else {
            echo json_encode(['error' => 'No se pudo crear la actividad']);
        }
    }

    /**
     * Actualiza el estado de una actividad (AJAX)
     */
    private function actualizarEstado(): void
    {
        header('Content-Type: application/json');

        // ✅ Verificar token CSRF
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSesion  = $_SESSION['csrf_token'] ?? '';
        if (empty($tokenEnviado) || empty($tokenSesion) || !hash_equals($tokenSesion, $tokenEnviado)) {
            unset($_SESSION['csrf_token']);
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF inválido.']);
            return;
        }
        unset($_SESSION['csrf_token']);

        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        $estado = trim($_POST['estado'] ?? '');

        if (!$id || empty($estado)) {
            echo json_encode(['error' => 'Parámetros inválidos']);
            return;
        }

        if ($this->modelo->actualizarEstado($id, $estado)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            echo json_encode(['error' => 'No se pudo actualizar el estado']);
        }
    }

    /**
     * Obtiene actividades del mes (AJAX)
     */
    public function getActividadesMes(): void
    {
        header('Content-Type: application/json');

        $mes = filter_var($_GET['mes'] ?? date('m'), FILTER_VALIDATE_INT);
        $anio = filter_var($_GET['anio'] ?? date('Y'), FILTER_VALIDATE_INT);

        $actividades = $this->modelo->getActividadesPorMes($mes, $anio);

        echo json_encode([
            'success' => true,
            'actividades' => $actividades,
        ]);
    }

    /**
     * Verifica que el usuario esté autenticado
     */
    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }
}