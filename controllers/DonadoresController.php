<?php
/**
 * controllers/DonadoresController.php
 *
 * CORRECCIONES:
 * 1. Se añadió el método privado redirigirConMensaje() igual al patrón de
 *    BeneficiariosController, eliminando la repetición de bloques
 *    $_SESSION + header + exit en cada validación fallida.
 * 2. Se corrigió el bug donde al fallar la validación de nombre/apellido
 *    se guardaba 'donadores_mensaje' pero NO 'donadores_tipo', dejando el
 *    tipo como null y mostrando el alert con estilo incorrecto en la vista.
 */
require_once 'models/DonadorModel.php';

class DonadoresController
{
    private DonadorModel $modelo;

    public function __construct(PDO $conexion)
    {
        $this->modelo = new DonadorModel($conexion);
    }

    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    private function verificarRol(array $rolesPermitidos): void
    {
        $rolActual = $_SESSION['rol'] ?? '';
        if (!in_array($rolActual, $rolesPermitidos, true)) {
            $_SESSION['error_acceso'] = 'No tienes permiso para acceder a este módulo.';
            header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
            exit;
        }
    }

    /**
     * Guarda un mensaje flash en sesión y redirige.
     * Centraliza el patrón $_SESSION + header + exit para evitar olvidar
     * el tipo de mensaje (error, success) en cada validación.
     */
    private function redirigirConMensaje(string $mensaje, string $tipo = 'success', string $url = ''): never
    {
        $_SESSION['donadores_mensaje'] = $mensaje;
        $_SESSION['donadores_tipo']    = $tipo;
        $destino = $url ?: BASE_URL . '/index.php?pagina=donadores';
        header('Location: ' . $destino);
        exit;
    }

    public function index(): void
    {
        $this->verificarSesion();
        $this->verificarRol(['Administrador']);

        $accion = trim($_GET['accion'] ?? 'listar');
        $id     = sanitizeInt($_GET['id'] ?? null);
        $metodo = $_SERVER['REQUEST_METHOD'];

        // ── POST: crear, editar, eliminar ─────────────────────────────────
        if ($metodo === 'POST') {
            if ($accion === 'crear') {
                $this->guardarNuevo();
            } elseif ($accion === 'editar' && $id) {
                $this->guardarEdicion($id);
            } elseif ($accion === 'eliminar' && $id) {
                $this->eliminarDonador($id);
            }
            return;
        }

        // ── GET: formularios y listado ────────────────────────────────────
        switch ($accion) {
            case 'crear':
                $this->mostrarFormularioNuevo();
                break;
            case 'editar':
                $id ? $this->mostrarFormularioEdicion($id) : $this->mostrarListado();
                break;
            case 'listar':
            default:
                $this->mostrarListado();
                break;
        }
    }

    private function mostrarListado(): void
    {
        $donadores    = $this->modelo->obtenerTodos();
        $mensaje      = $_SESSION['donadores_mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['donadores_tipo']    ?? 'success';
        unset($_SESSION['donadores_mensaje'], $_SESSION['donadores_tipo']);

        $total          = $this->modelo->obtenerTotal();
        $total_activos  = $this->modelo->obtenerTotalActivos();
        $total_por_tipo = $this->modelo->obtenerTotalPorTipo();

        $pagina_activa = 'donadores';
        $titulo_pagina = 'Donadores';
        require_once 'views/pages/DonadoresView.php';
    }

    private function mostrarFormularioNuevo(): void
    {
        $donador       = null;
        $pagina_activa = 'donadores';
        $titulo_pagina = 'Nuevo Donador';
        require_once 'views/pages/Donadores_FormView.php';
    }

    private function mostrarFormularioEdicion(int $id): void
    {
        $donador = $this->modelo->obtenerPorId($id);

        if (!$donador) {
            $this->redirigirConMensaje('Donador no encontrado.', 'error');
        }

        $pagina_activa = 'donadores';
        $titulo_pagina = 'Editar Donador';
        require_once 'views/pages/Donadores_FormView.php';
    }

    private function guardarNuevo(): void
    {
        csrfVerify();

        $tipoPersona = trim($_POST['tipo_persona'] ?? '');
        $email       = trim($_POST['email']        ?? '');
        $telefono    = trim($_POST['telefono']      ?? '');
        $puntos      = (int)($_POST['puntos_acumulados'] ?? 0);
        $activo      = isset($_POST['activo']);

        $urlCrear = BASE_URL . '/index.php?pagina=donadores&accion=crear';

        if (!in_array($tipoPersona, ['fisica', 'moral'], true)) {
            $this->redirigirConMensaje('Tipo de donador inválido.', 'error', $urlCrear);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('Email inválido.', 'error', $urlCrear);
        }

        if ($puntos < 0) {
            $this->redirigirConMensaje('Los puntos no pueden ser negativos.', 'error', $urlCrear);
        }

        $detalles = [];

        if ($tipoPersona === 'fisica') {
            $detalles['nombre']           = trim($_POST['nombre']   ?? '');
            $detalles['apellido']         = trim($_POST['apellido'] ?? '');
            $detalles['curp']             = trim($_POST['curp']     ?? '');
            $detalles['fecha_nacimiento'] = trim($_POST['fecha_nacimiento'] ?? '');

            if (empty($detalles['nombre']) || empty($detalles['apellido'])) {
                // CORRECCIÓN: antes faltaba el tipo 'error' aquí
                $this->redirigirConMensaje('El nombre y apellido son requeridos.', 'error', $urlCrear);
            }
        } else {
            $detalles['razon_social']        = trim($_POST['razon_social']        ?? '');
            $detalles['rfc']                 = trim($_POST['rfc']                 ?? '');
            $detalles['representante_legal'] = trim($_POST['representante_legal'] ?? '');
            $detalles['giro_comercial']      = trim($_POST['giro_comercial']      ?? '');

            if (empty($detalles['razon_social'])) {
                $this->redirigirConMensaje('La razón social es requerida.', 'error', $urlCrear);
            }
        }

        if ($this->modelo->crear($tipoPersona, $email, $telefono, $puntos, $activo, $detalles)) {
            $this->redirigirConMensaje('Donador creado correctamente.', 'success');
        } else {
            $this->redirigirConMensaje('Error al crear el donador.', 'error');
        }
    }

    private function guardarEdicion(int $id): void
    {
        csrfVerify();

        $tipoPersona = trim($_POST['tipo_persona'] ?? '');
        $email       = trim($_POST['email']        ?? '');
        $telefono    = trim($_POST['telefono']      ?? '');
        $puntos      = (int)($_POST['puntos_acumulados'] ?? 0);
        $activo      = isset($_POST['activo']);

        $urlEditar = BASE_URL . '/index.php?pagina=donadores&accion=editar&id=' . $id;

        if (!in_array($tipoPersona, ['fisica', 'moral'], true)) {
            $this->redirigirConMensaje('Tipo de donador inválido.', 'error', $urlEditar);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('Email inválido.', 'error', $urlEditar);
        }

        if ($puntos < 0) {
            $this->redirigirConMensaje('Los puntos no pueden ser negativos.', 'error', $urlEditar);
        }

        $detalles = [];

        if ($tipoPersona === 'fisica') {
            $detalles['nombre']           = trim($_POST['nombre']   ?? '');
            $detalles['apellido']         = trim($_POST['apellido'] ?? '');
            $detalles['curp']             = trim($_POST['curp']     ?? '');
            $detalles['fecha_nacimiento'] = trim($_POST['fecha_nacimiento'] ?? '');

            if (empty($detalles['nombre']) || empty($detalles['apellido'])) {
                $this->redirigirConMensaje('El nombre y apellido son requeridos.', 'error', $urlEditar);
            }
        } else {
            $detalles['razon_social']        = trim($_POST['razon_social']        ?? '');
            $detalles['rfc']                 = trim($_POST['rfc']                 ?? '');
            $detalles['representante_legal'] = trim($_POST['representante_legal'] ?? '');
            $detalles['giro_comercial']      = trim($_POST['giro_comercial']      ?? '');

            if (empty($detalles['razon_social'])) {
                $this->redirigirConMensaje('La razón social es requerida.', 'error', $urlEditar);
            }
        }

        if ($this->modelo->actualizar($id, $tipoPersona, $email, $telefono, $puntos, $activo, $detalles)) {
            $this->redirigirConMensaje('Donador actualizado correctamente.', 'success');
        } else {
            $this->redirigirConMensaje('Error al actualizar el donador.', 'error');
        }
    }

    private function eliminarDonador(int $id): void
    {
        csrfVerify();

        if ($this->modelo->eliminar($id)) {
            $this->redirigirConMensaje('Donador eliminado correctamente.', 'success');
        } else {
            $this->redirigirConMensaje('Error al eliminar el donador.', 'error');
        }
    }
}