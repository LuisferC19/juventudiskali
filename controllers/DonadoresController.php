<?php
require_once 'models/DonadorModel.php';

class DonadoresController {
    private DonadorModel $modelo;

    public function __construct(PDO $conexion) {
        $this->modelo = new DonadorModel($conexion);
    }

    private function verificarSesion(): void {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    public function index(): void {
        $this->verificarSesion();

        $accion = $_GET['accion'] ?? 'listar';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // Procesar acciones POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($accion === 'crear') {
                $this->guardarNuevo();
            } elseif ($accion === 'editar' && $id) {
                $this->guardarEdicion($id);
            } elseif ($accion === 'eliminar' && $id) {
                $this->eliminarDonador($id);
            }
            return;
        }

        // Procesar acciones GET
        switch ($accion) {
            case 'crear':
                $this->mostrarFormularioNuevo();
                break;
            case 'editar':
                if ($id) {
                    $this->mostrarFormularioEdicion($id);
                } else {
                    $this->mostrarListado();
                }
                break;
            case 'listar':
            default:
                $this->mostrarListado();
                break;
        }
    }

    /**
     * Mostrar listado de donadores
     */
    private function mostrarListado(): void {
        $donadores = $this->modelo->obtenerTodos();
        $mensaje = $_SESSION['donadores_mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['donadores_tipo'] ?? 'success';
        unset($_SESSION['donadores_mensaje'], $_SESSION['donadores_tipo']);

        // Estadísticas para las tarjetas
        $total = $this->modelo->obtenerTotal();
        $total_activos = $this->modelo->obtenerTotalActivos();
        $total_por_tipo = $this->modelo->obtenerTotalPorTipo();

        $pagina_activa = 'donadores';
        $titulo_pagina = 'Donadores';
        require_once 'views/pages/DonadoresView.php';
    }

    /**
     * Mostrar formulario para crear nuevo donador
     */
    private function mostrarFormularioNuevo(): void {
        $donador = null;
        $pagina_activa = 'donadores';
        $titulo_pagina = 'Nuevo Donador';
        require_once 'views/pages/Donadores_FormView.php';
    }

    /**
     * Mostrar formulario para editar donador
     */
    private function mostrarFormularioEdicion(int $id): void {
        $donador = $this->modelo->obtenerPorId($id);
        
        if (!$donador) {
            $_SESSION['donadores_mensaje'] = 'Donador no encontrado.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores');
            exit;
        }

        $pagina_activa = 'donadores';
        $titulo_pagina = 'Editar Donador';
        require_once 'views/pages/Donadores_FormView.php';
    }

    /**
     * Guardar nuevo donador (POST)
     */
    private function guardarNuevo(): void {
        $tipoPersona = trim($_POST['tipo_persona'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $puntos = (int) ($_POST['puntos_acumulados'] ?? 0);
        $activo = isset($_POST['activo']) ? true : false;

        if (!in_array($tipoPersona, ['fisica', 'moral'], true)) {
            $_SESSION['donadores_mensaje'] = 'Tipo de donador inválido.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=crear');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['donadores_mensaje'] = 'Email inválido.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=crear');
            exit;
        }

        if ($puntos < 0) {
            $_SESSION['donadores_mensaje'] = 'Los puntos no pueden ser negativos.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=crear');
            exit;
        }

        $detalles = [];

        if ($tipoPersona === 'fisica') {
            $detalles['nombre'] = trim($_POST['nombre'] ?? '');
            $detalles['apellido'] = trim($_POST['apellido'] ?? '');
            $detalles['curp'] = trim($_POST['curp'] ?? '');
            $detalles['fecha_nacimiento'] = trim($_POST['fecha_nacimiento'] ?? '');

            if (empty($detalles['nombre']) || empty($detalles['apellido'])) {
                $_SESSION['donadores_mensaje'] = 'El nombre y apellido son requeridos.';
                header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=crear');
                exit;
            }
        } else {
            $detalles['razon_social'] = trim($_POST['razon_social'] ?? '');
            $detalles['rfc'] = trim($_POST['rfc'] ?? '');
            $detalles['representante_legal'] = trim($_POST['representante_legal'] ?? '');
            $detalles['giro_comercial'] = trim($_POST['giro_comercial'] ?? '');

            if (empty($detalles['razon_social'])) {
                $_SESSION['donadores_mensaje'] = 'La razón social es requerida.';
                header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=crear');
                exit;
            }
        }

        if ($this->modelo->crear($tipoPersona, $email, $telefono, $puntos, $activo, $detalles)) {
            $_SESSION['donadores_mensaje'] = 'Donador creado correctamente.';
            $_SESSION['donadores_tipo'] = 'success';
        } else {
            $_SESSION['donadores_mensaje'] = 'Error al crear el donador.';
            $_SESSION['donadores_tipo'] = 'error';
        }

        header('Location: ' . BASE_URL . '/index.php?pagina=donadores');
        exit;
    }

    /**
     * Guardar cambios de donador existente (POST)
     */
    private function guardarEdicion(int $id): void {
        $tipoPersona = trim($_POST['tipo_persona'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $puntos = (int) ($_POST['puntos_acumulados'] ?? 0);
        $activo = isset($_POST['activo']) ? true : false;

        if (!in_array($tipoPersona, ['fisica', 'moral'], true)) {
            $_SESSION['donadores_mensaje'] = 'Tipo de donador inválido.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=editar&id=' . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['donadores_mensaje'] = 'Email inválido.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=editar&id=' . $id);
            exit;
        }

        if ($puntos < 0) {
            $_SESSION['donadores_mensaje'] = 'Los puntos no pueden ser negativos.';
            $_SESSION['donadores_tipo'] = 'error';
            header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=editar&id=' . $id);
            exit;
        }

        $detalles = [];

        if ($tipoPersona === 'fisica') {
            $detalles['nombre'] = trim($_POST['nombre'] ?? '');
            $detalles['apellido'] = trim($_POST['apellido'] ?? '');
            $detalles['curp'] = trim($_POST['curp'] ?? '');
            $detalles['fecha_nacimiento'] = trim($_POST['fecha_nacimiento'] ?? '');

            if (empty($detalles['nombre']) || empty($detalles['apellido'])) {
                $_SESSION['donadores_mensaje'] = 'El nombre y apellido son requeridos.';
                header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=editar&id=' . $id);
                exit;
            }
        } else {
            $detalles['razon_social'] = trim($_POST['razon_social'] ?? '');
            $detalles['rfc'] = trim($_POST['rfc'] ?? '');
            $detalles['representante_legal'] = trim($_POST['representante_legal'] ?? '');
            $detalles['giro_comercial'] = trim($_POST['giro_comercial'] ?? '');

            if (empty($detalles['razon_social'])) {
                $_SESSION['donadores_mensaje'] = 'La razón social es requerida.';
                header('Location: ' . BASE_URL . '/index.php?pagina=donadores&accion=editar&id=' . $id);
                exit;
            }
        }

        if ($this->modelo->actualizar($id, $tipoPersona, $email, $telefono, $puntos, $activo, $detalles)) {
            $_SESSION['donadores_mensaje'] = 'Donador actualizado correctamente.';
            $_SESSION['donadores_tipo'] = 'success';
        } else {
            $_SESSION['donadores_mensaje'] = 'Error al actualizar el donador.';
            $_SESSION['donadores_tipo'] = 'error';
        }

        header('Location: ' . BASE_URL . '/index.php?pagina=donadores');
        exit;
    }

    /**
     * Eliminar un donador
     */
    private function eliminarDonador(int $id): void {
        if ($this->modelo->eliminar($id)) {
            $_SESSION['donadores_mensaje'] = 'Donador eliminado correctamente.';
            $_SESSION['donadores_tipo'] = 'success';
        } else {
            $_SESSION['donadores_mensaje'] = 'Error al eliminar el donador.';
            $_SESSION['donadores_tipo'] = 'error';
        }

        header('Location: ' . BASE_URL . '/index.php?pagina=donadores');
        exit;
    }
}
