<?php
/**
 * controllers/UsuariosController.php
 * Controlador CRUD completo para el módulo de Usuarios.
 *
 * Rutas manejadas (todas bajo ?pagina=usuarios):
 *   GET  ?pagina=usuarios                      → lista de usuarios
 *   GET  ?pagina=usuarios&accion=nuevo         → formulario para crear
 *   POST ?pagina=usuarios&accion=crear         → guarda nuevo usuario
 *   GET  ?pagina=usuarios&accion=editar&id=N   → formulario de edición
 *   POST ?pagina=usuarios&accion=editar&id=N   → guarda cambios
 *   GET  ?pagina=usuarios&accion=borrar&id=N   → elimina usuario
 *   GET  ?pagina=usuarios&accion=desbloquear&id=N → resetea intentos fallidos
 */
require_once 'models/UsuarioModel.php';

class UsuariosController
{
    private UsuarioModel $modelo;
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db     = $conexion;
        $this->modelo = new UsuarioModel($conexion);
    }

    // =========================================================
    //  Router principal — llamado desde index.php
    // =========================================================

    public function index(): void
    {
        $this->verificarSesion();

        $accion = trim($_GET['accion'] ?? '');
        $id     = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
        $metodo = $_SERVER['REQUEST_METHOD'];

        // ── POST: Crear nuevo usuario
        if ($accion === 'crear' && $metodo === 'POST') {
            $this->crear();
            return;
        }

        // ── GET: Formulario vacío para crear
        if ($accion === 'nuevo' && $metodo === 'GET') {
            $this->formularioNuevo();
            return;
        }

        // ── GET / POST: Editar usuario existente
        if ($accion === 'editar' && $id) {
            if ($metodo === 'POST') {
                $this->actualizar((int)$id);
            } else {
                $this->formularioEditar((int)$id);
            }
            return;
        }

        // ── GET: Eliminar usuario
        if ($accion === 'borrar' && $id && $metodo === 'GET') {
            $this->eliminar((int)$id);
            return;
        }

        // ── GET: Desbloquear usuario (resetear intentos fallidos)
        if ($accion === 'desbloquear' && $id && $metodo === 'GET') {
            $this->desbloquear((int)$id);
            return;
        }

        // ── Por defecto: mostrar lista de usuarios
        $this->listar();
    }

    // =========================================================
    //  READ — GET: Lista de usuarios
    // =========================================================

    private function listar(): void
    {
        $usuarios = $this->modelo->consultar();
        $roles    = $this->modelo->obtenerRoles();

        $total         = count($usuarios);
        $total_activos = $this->modelo->obtenerTotalActivos();

        $mensaje      = $_SESSION['usuarios_mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['usuarios_tipo']    ?? 'success';
        unset($_SESSION['usuarios_mensaje'], $_SESSION['usuarios_tipo']);

        $pagina_activa = 'usuarios';
        $titulo_pagina = 'Usuarios del Sistema';
        require_once 'views/pages/UsuariosView.php';
    }

    // =========================================================
    //  CREATE — POST: Crear nuevo usuario
    // =========================================================

    private function crear(): void
    {
        $nombre             = trim((string)($_POST['nombre']             ?? ''));
        $apellido           = trim((string)($_POST['apellido']           ?? ''));
        $email              = trim((string)($_POST['email']              ?? ''));
        $password           = trim((string)($_POST['password']           ?? ''));
        $confirmar_password = trim((string)($_POST['confirmar_password'] ?? ''));
        $id_rol             = filter_var($_POST['id_rol'] ?? '', FILTER_VALIDATE_INT);
        $activo             = isset($_POST['activo']) ? true : false;

        // Forzar id_rol=5 (Donador) si el usuario no es administrador
        if (($_SESSION['rol'] ?? '') !== 'Administrador') {
            $id_rol = 5;
        }

        if (empty($nombre) || empty($apellido)) {
            $this->redirigirConMensaje('El nombre y el apellido son requeridos.', 'error');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('El correo electrónico no tiene un formato válido.', 'error');
        }
        if (strlen($password) < 6) {
            $this->redirigirConMensaje('La contraseña debe tener al menos 6 caracteres.', 'error');
        }
        if ($password !== $confirmar_password) {
            $this->redirigirConMensaje('Las contraseñas no coinciden.', 'error');
        }
        if (!$id_rol || $id_rol <= 0) {
            $this->redirigirConMensaje('Debes seleccionar un rol válido.', 'error');
        }
        if ($this->modelo->emailExiste($email)) {
            $this->redirigirConMensaje('El correo electrónico ya está registrado en el sistema.', 'error');
        }

        if ($this->modelo->insertar($nombre, $apellido, $email, $password, (int)$id_rol, $activo)) {
            $this->redirigirConMensaje("Usuario <strong>{$nombre} {$apellido}</strong> creado correctamente.", 'success');
        } else {
            $this->redirigirConMensaje('Error al crear el usuario. Intenta nuevamente.', 'error');
        }
    }

    // =========================================================
    //  READ (formulario) — GET: Mostrar form para crear
    // =========================================================

    private function formularioNuevo(): void
    {
        $roles         = $this->modelo->obtenerRoles();
        $accion        = 'nuevo';
        $usuario       = null;
        $pagina_activa = 'usuarios';
        $titulo_pagina = 'Nuevo Usuario';
        require_once 'views/pages/Usuarios_FormView.php';
    }

    // =========================================================
    //  READ (formulario) — GET: Mostrar form para editar
    // =========================================================

    private function formularioEditar(int $id): void
    {
        $usuario = $this->modelo->consultarPorId($id);

        if (!$usuario) {
            $this->redirigirConMensaje('Usuario no encontrado.', 'error');
        }

        $roles         = $this->modelo->obtenerRoles();
        $accion        = 'editar';
        $id_usuario    = $id;
        $pagina_activa = 'usuarios';
        $titulo_pagina = 'Editar Usuario';
        require_once 'views/pages/Usuarios_FormView.php';
    }

    // =========================================================
    //  UPDATE — POST: Actualizar usuario existente
    // =========================================================

    private function actualizar(int $id): void
    {
        $usuario = $this->modelo->consultarPorId($id);

        if (!$usuario) {
            $this->redirigirConMensaje('Usuario no encontrado.', 'error');
        }

        $nombre             = trim((string)($_POST['nombre']             ?? ''));
        $apellido           = trim((string)($_POST['apellido']           ?? ''));
        $email              = trim((string)($_POST['email']              ?? ''));
        $password           = trim((string)($_POST['password']           ?? ''));
        $confirmar_password = trim((string)($_POST['confirmar_password'] ?? ''));
        $id_rol             = filter_var($_POST['id_rol'] ?? '', FILTER_VALIDATE_INT);
        $activo             = isset($_POST['activo']) ? true : false;

        if (empty($nombre) || empty($apellido)) {
            $this->redirigirConMensaje('El nombre y el apellido son requeridos.', 'error');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('El correo electrónico no tiene un formato válido.', 'error');
        }
        if (strtolower($email) !== strtolower($usuario['email'])) {
            if ($this->modelo->emailExiste($email, $id)) {
                $this->redirigirConMensaje('El correo electrónico ya está en uso por otro usuario.', 'error');
            }
        }
        if (!$id_rol || $id_rol <= 0) {
            $this->redirigirConMensaje('Debes seleccionar un rol válido.', 'error');
        }

        $nuevaPassword = null;
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $this->redirigirConMensaje('La nueva contraseña debe tener al menos 6 caracteres.', 'error');
            }
            if ($password !== $confirmar_password) {
                $this->redirigirConMensaje('Las contraseñas no coinciden.', 'error');
            }
            $nuevaPassword = $password;
        }

        if ($this->modelo->actualizar($id, $nombre, $apellido, $email, (int)$id_rol, $activo, $nuevaPassword)) {
            $this->redirigirConMensaje("Usuario <strong>{$nombre} {$apellido}</strong> actualizado correctamente.", 'success');
        } else {
            $this->redirigirConMensaje('Error al actualizar el usuario. Intenta nuevamente.', 'error');
        }
    }

    // =========================================================
    //  DELETE — GET: Eliminar usuario
    // =========================================================

    private function eliminar(int $id): void
    {
        $usuario = $this->modelo->consultarPorId($id);

        if (!$usuario) {
            $this->redirigirConMensaje('Usuario no encontrado.', 'error');
        }

        if ($id === (int)$_SESSION['id_usuario']) {
            $this->redirigirConMensaje('No puedes eliminar tu propia cuenta de usuario.', 'error');
        }

        $resultado = $this->modelo->eliminar($id);

        if ($resultado === true) {
            // Eliminación física exitosa
            $this->redirigirConMensaje(
                "Usuario <strong>{$usuario['nombre']} {$usuario['apellido']}</strong> eliminado correctamente.",
                'success'
            );
        } else {
            // Baja lógica: tenía registros de negocio vinculados (campañas, donaciones, etc.)
            $this->redirigirConMensaje(
                "El usuario <strong>{$usuario['nombre']} {$usuario['apellido']}</strong> tiene registros vinculados " .
                "(campañas, donaciones, beneficiarios, etc.) y no puede eliminarse físicamente. " .
                "La cuenta fue <strong>desactivada</strong> en su lugar.",
                'warning'
            );
        }
    }

    // =========================================================
    //  EXTRA — GET: Desbloquear cuenta
    // =========================================================

    private function desbloquear(int $id): void
    {
        $usuario = $this->modelo->consultarPorId($id);

        if (!$usuario) {
            $this->redirigirConMensaje('Usuario no encontrado.', 'error');
        }

        if ($this->modelo->desbloquear($id)) {
            $this->redirigirConMensaje(
                "Cuenta de <strong>{$usuario['nombre']} {$usuario['apellido']}</strong> desbloqueada correctamente.",
                'success'
            );
        } else {
            $this->redirigirConMensaje('Error al desbloquear la cuenta. Intenta nuevamente.', 'error');
        }
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Verifica sesión activa; si no hay, redirige al login.
     */
    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    /**
     * Guarda un mensaje en sesión y redirige a la lista de usuarios.
     */
    private function redirigirConMensaje(string $mensaje, string $tipo = 'success'): void
    {
        $_SESSION['usuarios_mensaje'] = $mensaje;
        $_SESSION['usuarios_tipo']    = $tipo;
        header('Location: ' . BASE_URL . '/index.php?pagina=usuarios');
        exit;
    }
}