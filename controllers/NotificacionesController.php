<?php
/**
 * controllers/NotificacionesController.php
 * Controlador para gestionar notificaciones.
 */
require_once 'models/NotificacionModel.php';

class NotificacionesController
{
    private NotificacionModel $modelo;
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db     = $conexion;
        $this->modelo = new NotificacionModel($conexion);
    }

    /**
     * Router principal
     */
    public function index(): void
    {
        $this->verificarSesion();

        $accion = trim($_GET['accion'] ?? '');
        $metodo = $_SERVER['REQUEST_METHOD'];

        // AJAX: Marcar leída
        if ($accion === 'marcar_leida' && $metodo === 'POST') {
            $this->marcarLeida();
            return;
        }

        // AJAX: Marcar todas como leídas
        if ($accion === 'marcar_todas' && $metodo === 'POST') {
            $this->marcarTodasLeidas();
            return;
        }

        // AJAX: Obtener últimas notificaciones (para dropdown)
        if ($accion === 'ultimas' && $metodo === 'GET') {
            $this->obtenerUltimas();
            return;
        }

        // Mostrar lista completa de notificaciones
        $this->listar();
    }

    /**
     * Lista todas las notificaciones del usuario
     */
    private function listar(): void
    {
        $id_usuario = (int)($_SESSION['id_usuario'] ?? 0);
        $notificaciones = $this->modelo->getNotificaciones($id_usuario, 'web', false);
        $total_no_leidas = $this->modelo->contarNoLeidas($id_usuario, 'web');

        $pagina_activa = 'notificaciones';
        $titulo_pagina = 'Notificaciones';

        require_once 'views/layouts/header.php';
        require_once 'views/pages/NotificacionesView.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Marca una notificación como leída (AJAX)
     */
    private function marcarLeida(): void
    {
        header('Content-Type: application/json');

        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        if ($this->modelo->marcarLeida($id)) {
            echo json_encode(['success' => true, 'message' => 'Notificación marcada como leída']);
        } else {
            echo json_encode(['error' => 'No se pudo marcar la notificación']);
        }
    }

    /**
     * Marca todas las notificaciones como leídas (AJAX)
     */
    private function marcarTodasLeidas(): void
    {
        header('Content-Type: application/json');

        $id_usuario = (int)($_SESSION['id_usuario'] ?? 0);
        if (!$id_usuario) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            return;
        }

        if ($this->modelo->marcarTodasLeidas($id_usuario)) {
            echo json_encode(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
        } else {
            echo json_encode(['error' => 'No se pudieron marcar las notificaciones']);
        }
    }

    /**
     * Obtiene las últimas 5 notificaciones no leídas (AJAX)
     */
    private function obtenerUltimas(): void
    {
        header('Content-Type: application/json');

        $id_usuario = (int)($_SESSION['id_usuario'] ?? 0);
        if (!$id_usuario) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            return;
        }

        $notificaciones = $this->modelo->getUltimas($id_usuario, 5, 'web');
        $total_no_leidas = $this->modelo->contarNoLeidas($id_usuario, 'web');

        echo json_encode([
            'success'         => true,
            'notificaciones'  => $notificaciones,
            'total_no_leidas' => $total_no_leidas,
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
