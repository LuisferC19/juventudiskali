<?php
/**
 * controllers/UsuariosController.php
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
        $this->verificarRol(['Administrador']);

        $accion = trim($_GET['accion'] ?? '');
        $metodo = $_SERVER['REQUEST_METHOD'];
        // El id puede venir en GET (editar, desbloquear) o en POST oculto (borrar)
        $idRaw  = $_GET['id'] ?? $_POST['id'] ?? null;
        $id     = isset($idRaw) ? filter_var($idRaw, FILTER_VALIDATE_INT) : null;

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

        // ── POST: Eliminar usuario (requiere POST para evitar borrados por link/GET)
        if ($accion === 'borrar' && $id && $metodo === 'POST') {
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
        // CORRECCIÓN #5: verificar CSRF antes de procesar cualquier dato POST
        csrfVerify();

        $nombre             = trim((string)($_POST['nombre']             ?? ''));
        $apellido           = trim((string)($_POST['apellido']           ?? ''));
        $email              = trim((string)($_POST['email']              ?? ''));
        $password           = trim((string)($_POST['password']           ?? ''));
        $confirmar_password = trim((string)($_POST['confirmar_password'] ?? ''));
        $id_rol             = filter_var($_POST['id_rol'] ?? '', FILTER_VALIDATE_INT);
        $activo             = isset($_POST['activo']) ? true : false;

        if (($_SESSION['rol'] ?? '') !== 'Administrador') {
            $id_rol = 5;
        }

        // ── Validaciones de caracteres  ──
        if (empty($nombre) || empty($apellido)) {
            $this->redirigirConMensaje('El nombre y el apellido son requeridos.', 'error');
        }
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u', $nombre)) {
            $this->redirigirConMensaje('El nombre solo puede contener letras y espacios.', 'error');
        }
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
            $this->redirigirConMensaje('El nombre debe tener entre 2 y 100 caracteres.', 'error');
        }
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u', $apellido)) {
            $this->redirigirConMensaje('El apellido solo puede contener letras y espacios.', 'error');
        }
        if (mb_strlen($apellido) < 2 || mb_strlen($apellido) > 100) {
            $this->redirigirConMensaje('El apellido debe tener entre 2 y 100 caracteres.', 'error');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('El correo electrónico no tiene un formato válido.', 'error');
        }
        if (mb_strlen($email) > 150) {
            $this->redirigirConMensaje('El correo electrónico no puede superar los 150 caracteres.', 'error');
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
            // CORRECCIÓN #6: nombre se pasa escapado, no interpolado con HTML crudo
            $this->redirigirConMensaje(
                'Usuario <strong>' . e($nombre) . ' ' . e($apellido) . '</strong> creado correctamente.',
                'success'
            );
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
        // CORRECCIÓN #5: verificar CSRF antes de procesar cualquier dato POST
        csrfVerify();

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

        // ── Validaciones de caracteres ──
        if (empty($nombre) || empty($apellido)) {
            $this->redirigirConMensaje('El nombre y el apellido son requeridos.', 'error');
        }
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u', $nombre)) {
            $this->redirigirConMensaje('El nombre solo puede contener letras y espacios.', 'error');
        }
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
            $this->redirigirConMensaje('El nombre debe tener entre 2 y 100 caracteres.', 'error');
        }
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u', $apellido)) {
            $this->redirigirConMensaje('El apellido solo puede contener letras y espacios.', 'error');
        }
        if (mb_strlen($apellido) < 2 || mb_strlen($apellido) > 100) {
            $this->redirigirConMensaje('El apellido debe tener entre 2 y 100 caracteres.', 'error');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirigirConMensaje('El correo electrónico no tiene un formato válido.', 'error');
        }
        if (mb_strlen($email) > 150) {
            $this->redirigirConMensaje('El correo electrónico no puede superar los 150 caracteres.', 'error');
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
            // CORRECCIÓN #6: nombre se pasa escapado, no interpolado con HTML crudo
            $this->redirigirConMensaje(
                'Usuario <strong>' . e($nombre) . ' ' . e($apellido) . '</strong> actualizado correctamente.',
                'success'
            );
        } else {
            $this->redirigirConMensaje('Error al actualizar el usuario. Intenta nuevamente.', 'error');
        }
    }

    // =========================================================
    //  DELETE — POST: Eliminar usuario
    // =========================================================

    private function eliminar(int $id): void
    {
        csrfVerify();

        $usuario = $this->modelo->consultarPorId($id);

        if (!$usuario) {
            $this->redirigirConMensaje('Usuario no encontrado.', 'error');
        }

        if ($id === (int)$_SESSION['id_usuario']) {
            $this->redirigirConMensaje('No puedes eliminar tu propia cuenta de usuario.', 'error');
        }

        $resultado = $this->modelo->eliminar($id);

        if ($resultado === true) {
            // CORRECCIÓN #6: nombre escapado con e()
            $this->redirigirConMensaje(
                'Usuario <strong>' . e($usuario['nombre']) . ' ' . e($usuario['apellido']) . '</strong> eliminado correctamente.',
                'success'
            );
        } else {
            // CORRECCIÓN #6: nombre escapado con e()
            $this->redirigirConMensaje(
                'El usuario <strong>' . e($usuario['nombre']) . ' ' . e($usuario['apellido']) . '</strong> tiene registros vinculados ' .
                'y no puede eliminarse físicamente. La cuenta fue <strong>desactivada</strong> en su lugar.',
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
            // CORRECCIÓN #6: nombre escapado con e()
            $this->redirigirConMensaje(
                'Cuenta de <strong>' . e($usuario['nombre']) . ' ' . e($usuario['apellido']) . '</strong> desbloqueada correctamente.',
                'success'
            );
        } else {
            $this->redirigirConMensaje('Error al desbloquear la cuenta. Intenta nuevamente.', 'error');
        }
    }

    // =========================================================
    //  HELPERS
    // =========================================================

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

    private function redirigirConMensaje(string $mensaje, string $tipo = 'success'): void
    {
        $_SESSION['usuarios_mensaje'] = $mensaje;
        $_SESSION['usuarios_tipo']    = $tipo;
        header('Location: ' . BASE_URL . '/index.php?pagina=usuarios');
        exit;
    }
}