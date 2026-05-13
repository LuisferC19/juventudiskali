<?php
/**
 * controllers/VoluntariosController.php
 * Controlador para gestionar voluntarios y asistencia.
 */
require_once 'models/VoluntarioModel.php';

class VoluntariosController
{
    private VoluntarioModel $modelo;
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db     = $conexion;
        $this->modelo = new VoluntarioModel($conexion);
    }

    /**
     * Router principal
     */
    public function index(): void
    {
        $this->verificarSesion();

        $accion = trim($_GET['accion'] ?? '');
        $metodo = $_SERVER['REQUEST_METHOD'];

        // AJAX: Registrar asistencia
        if ($accion === 'registrar_asistencia' && $metodo === 'POST') {
            $this->registrarAsistencia();
            return;
        }

        // AJAX: Generar token QR
        if ($accion === 'generar_qr' && $metodo === 'POST') {
            $this->generarTokenQR();
            return;
        }

        // AJAX: Validar QR
        if ($accion === 'validar_qr' && $metodo === 'POST') {
            $this->validarQR();
            return;
        }

        // AJAX: Obtener asistencia de actividad
        if ($accion === 'obtener_asistencia' && $metodo === 'GET') {
            $this->obtenerAsistencia();
            return;
        }

        // Mostrar página de voluntarios
        $this->mostrarVoluntarios();
    }

    /**
     * Muestra la página de voluntarios
     */
    private function mostrarVoluntarios(): void
    {
        $voluntarios = $this->modelo->getVoluntariosActivos();
        $actividades = $this->modelo->getActividadesActivas();

        $pagina_activa = 'voluntarios';
        $titulo_pagina = 'Voluntarios';

        require_once 'views/layouts/header.php';
        require_once 'views/pages/VoluntariosView.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Registra asistencia manual de un voluntario (AJAX)
     */
    private function registrarAsistencia(): void
    {
        header('Content-Type: application/json');

        $id_voluntario = filter_var($_POST['id_voluntario'] ?? 0, FILTER_VALIDATE_INT);
        $id_actividad = filter_var($_POST['id_actividad'] ?? 0, FILTER_VALIDATE_INT);

        if (!$id_voluntario || !$id_actividad) {
            echo json_encode(['error' => 'Parámetros inválidos']);
            return;
        }

        $data = [
            'id_voluntario' => $id_voluntario,
            'id_actividad'  => $id_actividad,
            'presente'      => 1,
            'metodo'        => 'manual',
        ];

        if ($this->modelo->registrarAsistencia($data)) {
            echo json_encode(['success' => true, 'message' => 'Asistencia registrada correctamente']);
        } else {
            echo json_encode(['error' => 'No se pudo registrar la asistencia']);
        }
    }

    /**
     * Genera un token QR para una actividad (AJAX)
     */
    private function generarTokenQR(): void
    {
        header('Content-Type: application/json');

        $id_actividad = filter_var($_POST['id_actividad'] ?? 0, FILTER_VALIDATE_INT);

        if (!$id_actividad) {
            echo json_encode(['error' => 'ID de actividad inválido']);
            return;
        }

        $token = bin2hex(random_bytes(32));

        if ($token) {
            echo json_encode([
                'success' => true,
                'token' => $token,
                'message' => 'Token QR generado correctamente',
            ]);
        } else {
            echo json_encode(['error' => 'No se pudo generar el token QR']);
        }
    }

    /**
     * Valida un QR y marca al voluntario como presente (AJAX)
     */
    private function validarQR(): void
    {
        header('Content-Type: application/json');

        $token_qr = trim($_POST['token_qr'] ?? '');

        if (empty($token_qr)) {
            echo json_encode(['error' => 'Token QR no proporcionado']);
            return;
        }

        if ($this->modelo->validarQR($token_qr)) {
            echo json_encode(['success' => true, 'message' => 'Asistencia confirmada por QR']);
        } else {
            echo json_encode(['error' => 'Token QR inválido']);
        }
    }

    /**
     * Obtiene la asistencia de voluntarios para una actividad (AJAX)
     */
    private function obtenerAsistencia(): void
    {
        header('Content-Type: application/json');

        $id_actividad = filter_var($_GET['id_actividad'] ?? 0, FILTER_VALIDATE_INT);

        if (!$id_actividad) {
            echo json_encode(['error' => 'ID de actividad inválido']);
            return;
        }

        $asistencia = $this->modelo->getAsistenciaPorActividad($id_actividad);

        if (is_array($asistencia)) {
            echo json_encode(['success' => true, 'asistencia' => $asistencia]);
        } else {
            echo json_encode(['error' => 'No se pudo obtener la asistencia']);
        }
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
