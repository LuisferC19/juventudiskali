<?php
/**
 * controllers/BeneficiariosController.php
 * Controlador CRUD para beneficiarios.
 */
require_once 'models/BeneficiarioModel.php';

class BeneficiariosController
{
    private BeneficiarioModel $modelo;
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db     = $conexion;
        $this->modelo = new BeneficiarioModel($conexion);
    }

    public function index(): void
    {
        $this->verificarSesion();

        $accion = trim($_GET['accion'] ?? '');
        $id     = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($accion === 'crear' && $metodo === 'POST') {
            $this->crear();
            return;
        }

        if ($accion === 'nuevo' && $metodo === 'GET') {
            $this->formularioNuevo();
            return;
        }

        if ($accion === 'editar' && $id) {
            if ($metodo === 'POST') {
                $this->actualizar((int)$id);
            } else {
                $this->formularioEditar((int)$id);
            }
            return;
        }

        if ($accion === 'borrar' && $id && $metodo === 'GET') {
            $this->eliminar((int)$id);
            return;
        }

        $this->listar();
    }

    private function listar(): void
    {
        $beneficiarios = $this->modelo->consultar();
        $total         = count($beneficiarios);
        $total_activos = $this->modelo->obtenerTotalActivos();

        $mensaje      = $_SESSION['beneficiarios_mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['beneficiarios_tipo']    ?? 'success';
        unset($_SESSION['beneficiarios_mensaje'], $_SESSION['beneficiarios_tipo']);

        $pagina_activa = 'beneficiarios';
        $titulo_pagina = 'Beneficiarios';
        require_once 'views/pages/beneficiarios.php';
    }

    private function crear(): void
    {
        $nombre_completo = trim((string)($_POST['nombre_completo'] ?? ''));
        $edad            = isset($_POST['edad']) ? filter_var($_POST['edad'], FILTER_VALIDATE_INT) : null;
        $id_comunidad    = filter_var($_POST['id_comunidad'] ?? '', FILTER_VALIDATE_INT);
        $direccion       = trim((string)($_POST['direccion'] ?? ''));
        $telefono        = trim((string)($_POST['telefono'] ?? ''));
        $estado          = trim((string)($_POST['estado'] ?? ''));
        $notas           = trim((string)($_POST['notas'] ?? ''));
        $registrador     = (int)$_SESSION['id_usuario'];

        if ($nombre_completo === '') {
            $this->redirigirConMensaje('El nombre completo es obligatorio.', 'error');
        }

        if (!$id_comunidad || $id_comunidad <= 0) {
            $this->redirigirConMensaje('Debes seleccionar una comunidad válida.', 'error');
        }

        $estadosValidos = ['activo', 'inactivo', 'en_espera'];
        if (!in_array($estado, $estadosValidos, true)) {
            $this->redirigirConMensaje('Selecciona un estado válido para el beneficiario.', 'error');
        }

        if ($edad !== null && $edad < 0) {
            $this->redirigirConMensaje('La edad debe ser un número positivo.', 'error');
        }

        if ($this->modelo->insertar(
            $nombre_completo,
            $edad,
            $id_comunidad,
            $direccion,
            $telefono,
            $estado,
            $notas,
            $registrador
        )) {
            $this->redirigirConMensaje("Beneficiario <strong>{$nombre_completo}</strong> creado correctamente.", 'success');
        }

        $this->redirigirConMensaje('Error al crear el beneficiario. Intenta nuevamente.', 'error');
    }

    private function formularioNuevo(): void
    {
        $comunidades   = $this->modelo->consultarComunidades();
        $beneficiario  = null;
        $accion        = 'nuevo';
        $pagina_activa = 'beneficiarios';
        $titulo_pagina = 'Nuevo Beneficiario';
        require_once 'views/pages/beneficiario_form.php';
    }

    private function formularioEditar(int $id): void
    {
        $beneficiario = $this->modelo->consultarPorId($id);

        if (!$beneficiario) {
            $this->redirigirConMensaje('Beneficiario no encontrado.', 'error');
        }

        $comunidades   = $this->modelo->consultarComunidades();
        $accion        = 'editar';
        $pagina_activa = 'beneficiarios';
        $titulo_pagina = 'Editar Beneficiario';
        require_once 'views/pages/beneficiario_form.php';
    }

    private function actualizar(int $id): void
    {
        $beneficiario = $this->modelo->consultarPorId($id);

        if (!$beneficiario) {
            $this->redirigirConMensaje('Beneficiario no encontrado.', 'error');
        }

        $nombre_completo = trim((string)($_POST['nombre_completo'] ?? ''));
        $edad            = isset($_POST['edad']) ? filter_var($_POST['edad'], FILTER_VALIDATE_INT) : null;
        $id_comunidad    = filter_var($_POST['id_comunidad'] ?? '', FILTER_VALIDATE_INT);
        $direccion       = trim((string)($_POST['direccion'] ?? ''));
        $telefono        = trim((string)($_POST['telefono'] ?? ''));
        $estado          = trim((string)($_POST['estado'] ?? ''));
        $notas           = trim((string)($_POST['notas'] ?? ''));

        if ($nombre_completo === '') {
            $this->redirigirConMensaje('El nombre completo es obligatorio.', 'error');
        }

        if (!$id_comunidad || $id_comunidad <= 0) {
            $this->redirigirConMensaje('Debes seleccionar una comunidad válida.', 'error');
        }

        $estadosValidos = ['activo', 'inactivo', 'en_espera'];
        if (!in_array($estado, $estadosValidos, true)) {
            $this->redirigirConMensaje('Selecciona un estado válido para el beneficiario.', 'error');
        }

        if ($edad !== null && $edad < 0) {
            $this->redirigirConMensaje('La edad debe ser un número positivo.', 'error');
        }

        if ($this->modelo->actualizar(
            $id,
            $nombre_completo,
            $edad,
            $id_comunidad,
            $direccion,
            $telefono,
            $estado,
            $notas
        )) {
            $this->redirigirConMensaje("Beneficiario <strong>{$nombre_completo}</strong> actualizado correctamente.", 'success');
        }

        $this->redirigirConMensaje('Error al actualizar el beneficiario. Intenta nuevamente.', 'error');
    }

    private function eliminar(int $id): void
    {
        $beneficiario = $this->modelo->consultarPorId($id);

        if (!$beneficiario) {
            $this->redirigirConMensaje('Beneficiario no encontrado.', 'error');
        }

        if ($this->modelo->eliminar($id)) {
            $this->redirigirConMensaje("Beneficiario <strong>{$beneficiario['nombre_completo']}</strong> eliminado correctamente.", 'success');
        }

        $this->redirigirConMensaje('Error al eliminar el beneficiario. Intenta nuevamente.', 'error');
    }

    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    private function redirigirConMensaje(string $mensaje, string $tipo = 'success'): void
    {
        $_SESSION['beneficiarios_mensaje'] = $mensaje;
        $_SESSION['beneficiarios_tipo']    = $tipo;
        header('Location: ' . BASE_URL . '/index.php?pagina=beneficiarios');
        exit;
    }
}
