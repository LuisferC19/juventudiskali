<?php
/**
 * controllers/AuthController.php
 * Maneja login y logout conectado a la base de datos 'iskali'.
 *
 * FIX DE SESIÓN:
 * Se agrega session_write_close() JUSTO ANTES de cada header(Location)+exit.
 * Esto obliga a PHP a escribir la sesión en disco antes del redirect,
 * previniendo condiciones de carrera en Laragon/Windows donde el archivo
 * de sesión a veces no se escribe a tiempo y la siguiente petición lo ve vacío.
 */
class AuthController
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Muestra el formulario de login o procesa el POST.
     */
    public function login(): void
    {
        // Si ya hay sesión activa, redirigir al dashboard
        if (!empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Por favor ingresa tu correo y contraseña.';
            } else {
                $stmt = $this->db->prepare("
                    SELECT u.id_usuario, u.nombre, u.apellido, u.email,
                           u.contrasena_hash, u.activo, u.intentos_fallidos,
                           r.nombre AS rol
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.email = ?
                    LIMIT 1
                ");
                $stmt->execute([strtolower(trim($email))]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$usuario) {
                    $error = 'Credenciales incorrectas.';

                } elseif (!$usuario['activo']) {
                    $error = 'Tu cuenta está desactivada. Contacta al administrador.';

                } elseif ($usuario['intentos_fallidos'] >= 5) {
                    $error = 'Cuenta bloqueada por demasiados intentos fallidos. Contacta al administrador.';

                } elseif (!password_verify($password, $usuario['contrasena_hash'])) {
                    $this->db->prepare("
                        UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id_usuario = ?
                    ")->execute([$usuario['id_usuario']]);

                    $intentosRestantes = max(0, 5 - ($usuario['intentos_fallidos'] + 1));
                    $error = "Credenciales incorrectas. Te quedan {$intentosRestantes} intento(s).";

                } else {
                    // ✅ Login exitoso

                    // Resetear intentos y guardar último acceso
                    $this->db->prepare("
                        UPDATE usuarios
                        SET intentos_fallidos = 0, ultimo_acceso = NOW()
                        WHERE id_usuario = ?
                    ")->execute([$usuario['id_usuario']]);

                    // Regenerar ID de sesión por seguridad (previene session fixation)
                    session_regenerate_id(false);

                    // Guardar datos en sesión
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['usuario']    = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $_SESSION['nombre']     = $usuario['nombre'];
                    $_SESSION['email']      = $usuario['email'];
                    $_SESSION['rol']        = $usuario['rol'];

                    // ✅ session_write_close() aquí fuerza escritura del archivo de sesión
                    // antes del redirect. Crítico en Laragon/Windows para evitar que la
                    // siguiente petición vea $_SESSION vacío y regrese al login.
                    session_write_close();

                    header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
                    exit;
                }
            }
        }

        require_once 'views/pages/LoginView.php';
    }

    /**
     * Cierra la sesión y redirige al login.
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?pagina=login');
        exit;
    }
}