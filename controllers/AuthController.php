<?php
/**
 * controllers/AuthController.php
 *
 * MEJORAS:
 * - Se agrega verificación CSRF en el POST del login.
 * - Se usa csrfField() en la vista (recuerda agregarlo en LoginView.php).
 * - Se consolida el manejo de errores con un array en vez de concatenar strings.
 * - Pequeño hardening: email siempre se procesa en minúsculas de forma consistente.
 */
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';

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
        if (!empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfVerify();

            $email    = strtolower(trim($_POST['email']    ?? ''));
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Por favor ingresa tu correo y contraseña.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El formato del correo no es válido.';
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
                $stmt->execute([$email]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$usuario) {
                    $error = 'Credenciales incorrectas.';
                } elseif (!$usuario['activo']) {
                    $error = 'Tu cuenta está desactivada. Contacta al administrador.';
                } elseif ((int)$usuario['intentos_fallidos'] >= 5) {
                    $error = 'Cuenta bloqueada por demasiados intentos fallidos. Contacta al administrador.';
                } elseif (!password_verify($password, $usuario['contrasena_hash'])) {
                    $this->db->prepare("
                        UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id_usuario = ?
                    ")->execute([$usuario['id_usuario']]);

                    if ((int)$usuario['intentos_fallidos'] + 1 >= 5) {
                        $this->notificarCuentaBloqueada($usuario);
                    }
                    $intentosRestantes = max(0, 5 - ((int)$usuario['intentos_fallidos'] + 1));
                    $error = "Credenciales incorrectas. Te quedan {$intentosRestantes} intento(s).";
                } else {
                    $this->db->prepare("
                        UPDATE usuarios
                        SET intentos_fallidos = 0, ultimo_acceso = NOW()
                        WHERE id_usuario = ?
                    ")->execute([$usuario['id_usuario']]);

                    session_regenerate_id(true);

                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['usuario']    = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $_SESSION['nombre']     = $usuario['nombre'];
                    $_SESSION['email']      = $usuario['email'];
                    $_SESSION['rol']        = $usuario['rol'];

                    session_write_close();
                    header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
                    exit;
                }
            }
        }

        require_once 'views/pages/LoginView.php';
    }

    public function registro(): void
    {
        $error   = '';
        $success = '';
        $form    = [
            'nombre'  => trim((string)($_POST['nombre'] ?? '')),
            'apellido'=> trim((string)($_POST['apellido'] ?? '')),
            'email'   => strtolower(trim((string)($_POST['email'] ?? ''))),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password       = (string)($_POST['password'] ?? '');
            $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

            if ($form['nombre'] === '' || $form['apellido'] === '') {
                $error = 'Ingresa tu nombre y apellido.';
            } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresa un correo válido.';
            } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password)) {
                $error = 'La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                $modelo = new UsuarioModel($this->db);

                if ($modelo->emailExiste($form['email'])) {
                    $error = 'Este correo ya está registrado.';
                } else {
                    $rolId = $this->obtenerRolIdPorNombre('Voluntario');
                    if ($rolId === null) {
                        $rolId = 3;
                    }

                    if ($modelo->insertar($form['nombre'], $form['apellido'], $form['email'], $password, $rolId, false)) {
                        $success = 'Tu solicitud fue enviada correctamente. Un administrador revisará tu acceso.';
                        $form = ['nombre' => '', 'apellido' => '', 'email' => ''];
                    } else {
                        $error = 'No se pudo completar el registro. Intenta nuevamente.';
                    }
                }
            }
        }

        require_once 'views/pages/RegistroView.php';
    }

    public function recuperarPassword(): void
    {
        $error   = '';
        $success = '';
        $email   = strtolower(trim((string)($_POST['email'] ?? '')));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresa un correo electrónico válido.';
            } else {
                $usuario = $this->buscarUsuarioPorEmail($email);

                if (!$usuario) {
                    $success = 'Si el correo está asociado a una cuenta, recibirás instrucciones para restablecer la contraseña.';
                    $email = '';
                } else {
                    $token = bin2hex(random_bytes(32));
                    $fechaExpiracion = date('Y-m-d H:i:s', time() + 3600);

                    $stmt = $this->db->prepare("
                        INSERT INTO recuperacion_contrasena (id_usuario, token, fecha_solicitud, fecha_expiracion, usado)
                        VALUES (?, ?, NOW(), ?, 0)
                    ");
                    $stmt->execute([(int)$usuario['id_usuario'], $token, $fechaExpiracion]);

                    $success = 'Si el correo está asociado a una cuenta, recibirás instrucciones para restablecer la contraseña.';
                    $email = '';
                }
            }
        }

        require_once 'views/pages/RecuperarPasswordView.php';
    }

    private function obtenerRolIdPorNombre(string $nombre): ?int
    {
        $stmt = $this->db->prepare('SELECT id_rol FROM roles WHERE LOWER(nombre) = LOWER(?) LIMIT 1');
        $stmt->execute([$nombre]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false && isset($row['id_rol']) ? (int)$row['id_rol'] : null;
    }

    private function buscarUsuarioPorEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT id_usuario, nombre, apellido, email FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    private function notificarCuentaBloqueada(array $usuario): void
    {
        try {
            $usuarios = $this->db->query(
                "SELECT id_usuario FROM usuarios
                 WHERE id_rol IN (SELECT id_rol FROM roles WHERE nombre = 'Administrador')"
            )->fetchAll(PDO::FETCH_COLUMN);
            $notificaciones = new NotificacionModel($this->db);
            foreach ($usuarios as $idUsuario) {
                try {
                    $notificaciones->crear([
                        'id_usuario' => (int)$idUsuario,
                        'tipo' => 'alerta',
                        'asunto' => 'Cuenta bloqueada por intentos fallidos',
                        'mensaje' => 'La cuenta de ' . trim($usuario['nombre'] . ' ' . $usuario['apellido']) . ' (' . $usuario['email'] . ') alcanzó cinco intentos fallidos.',
                        'canal' => 'web',
                        'id_tipo_ref' => null,
                        'id_referencia' => (int)$usuario['id_usuario'],
                    ]);
                } catch (Throwable $error) {
                    logger('No se pudo notificar cuenta bloqueada: ' . $error->getMessage());
                }
            }
        } catch (Throwable $error) {
            logger('No se pudo preparar notificación de cuenta bloqueada: ' . $error->getMessage());
        }
    }

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