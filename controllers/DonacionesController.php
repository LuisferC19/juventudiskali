<?php
/**
 * controllers/DonacionesController.php
 * Registro y seguimiento de donaciones.
 */
require_once 'models/DonacionModel.php';
require_once 'models/NotificacionModel.php';

class DonacionesController
{
    private PDO $db;
    private DonacionModel $modelo;
    private NotificacionModel $notificaciones;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
        $this->modelo = new DonacionModel($conexion);
        $this->notificaciones = new NotificacionModel($conexion);
    }

    public function index(): void
    {
        $this->verificarSesion();
        $this->verificarRol(['Administrador', 'Coordinador', 'Auditor', 'Captador', 'Analista', 'Legal']);
        if (!usuarioPuede('donaciones', 'ver')) {
            $this->redirigirConMensaje('No tienes permiso para ver donaciones.', 'error', BASE_URL . '/index.php?pagina=dashboard');
        }

        $accion = trim((string)($_GET['accion'] ?? 'listar'));
        $id = sanitizeInt($_GET['id'] ?? null);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($accion === 'guardar') {
                $this->verificarPermiso('crear');
                $this->guardarNuevo();
            } elseif ($accion === 'cambiar-estado' && $id) {
                $this->verificarPermiso('editar');
                $this->cambiarEstado($id);
            } else {
                $this->redirigirConMensaje('Acción de donación inválida.', 'error');
            }
            return;
        }
        if ($accion === 'crear') {
            $this->verificarPermiso('crear');
            $this->mostrarFormularioNuevo();
            return;
        }
        $this->listar();
    }

    private function listar(): void
    {
        $estado = trim((string)($_GET['estado'] ?? ''));
        if ($estado !== '' && !in_array($estado, ['pendiente', 'recibida', 'verificada', 'rechazada'], true)) {
            $estado = '';
        }
        $idCampana = sanitizeInt($_GET['id_campana'] ?? null);
        $donaciones = $this->modelo->listar($estado ?: null, $idCampana);
        $campanas = $this->modelo->listarCampanas();
        $total = count($donaciones);
        $total_pendientes = count(array_filter($donaciones, static fn(array $d): bool => $d['estado'] === 'pendiente'));
        $total_verificadas = count(array_filter($donaciones, static fn(array $d): bool => $d['estado'] === 'verificada'));
        $mensaje = $_SESSION['mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'success';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
        $pagina_activa = 'donaciones';
        $titulo_pagina = 'Donaciones';
        $conexion = $this->db;
        require 'views/pages/DonacionesView.php';
    }

    private function mostrarFormularioNuevo(): void
    {
        $donadores = $this->modelo->listarDonadores();
        $campanas = $this->modelo->listarCampanasActivas();
        $tipos_bien = $this->modelo->listarTiposBien();
        $pagina_activa = 'donaciones';
        $titulo_pagina = 'Nueva donación';
        require 'views/pages/Donaciones_FormView.php';
    }

    private function guardarNuevo(): void
    {
        csrfVerify();
        $tipo = trim((string)($_POST['tipo'] ?? ''));
        $donador = sanitizeInt($_POST['id_donador'] ?? null);
        $campana = sanitizeInt($_POST['id_campana'] ?? null);
        $fecha = trim((string)($_POST['fecha_recepcion'] ?? ''));
        $url = trim((string)($_POST['evidencia_url'] ?? ''));
        $datos = [
            'tipo' => $tipo,
            'id_donador' => $donador,
            'id_campana' => $campana,
            'id_usuario_registrador' => (int)$_SESSION['id_usuario'],
            'fecha_recepcion' => $fecha,
            'evidencia_url' => $url !== '' ? $url : null,
            'id_tipo_bien' => sanitizeInt($_POST['id_tipo_bien'] ?? null),
            'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
            'cantidad' => trim((string)($_POST['cantidad'] ?? '')),
            'unidad_medida' => trim((string)($_POST['unidad_medida'] ?? '')),
            'monto' => trim((string)($_POST['monto'] ?? '')),
            'moneda' => trim((string)($_POST['moneda'] ?? 'MXN')),
            'metodo_pago' => trim((string)($_POST['metodo_pago'] ?? '')),
            'referencia_pago' => trim((string)($_POST['referencia_pago'] ?? '')),
        ];
        $dateValid = (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)
            && DateTime::createFromFormat('!Y-m-d', $fecha)?->format('Y-m-d') === $fecha;
        $valid = in_array($tipo, ['especie', 'economica'], true)
            && $donador !== null && $campana !== null && $dateValid
            && ($url === '' || filter_var($url, FILTER_VALIDATE_URL) !== false);
        if ($tipo === 'especie') {
            $valid = $valid && $datos['id_tipo_bien'] !== null && is_numeric($datos['cantidad']) && (float)$datos['cantidad'] > 0;
        } elseif ($tipo === 'economica') {
            $valid = $valid && is_numeric($datos['monto']) && (float)$datos['monto'] > 0
                && in_array($datos['metodo_pago'], ['efectivo', 'transferencia', 'cheque', 'otro'], true)
                && preg_match('/^[A-Z]{3}$/', $datos['moneda']);
        }
        if (!$valid) {
            $this->redirigirConMensaje('Revisa los datos requeridos de la donación.', 'error', BASE_URL . '/index.php?pagina=donaciones&accion=crear');
        }
        $ok = $this->modelo->crear($datos);
        if ($ok) {
            $this->notificarNuevaDonacion((int)$this->db->lastInsertId(), $donador, $campana);
        }
        $this->redirigirConMensaje($ok ? 'Donación registrada correctamente.' : 'No se pudo registrar la donación.', $ok ? 'success' : 'error');
    }

    private function cambiarEstado(int $id): void
    {
        csrfVerify();
        $estado = trim((string)($_POST['estado'] ?? ''));
        $ok = $this->modelo->cambiarEstado($id, $estado);
        if ($ok && $estado === 'verificada') {
            $this->notificarDonacionVerificada($id);
        }
        $this->redirigirConMensaje($ok ? 'Estado de donación actualizado.' : 'Transición de estado inválida o donación inexistente.', $ok ? 'success' : 'error');
    }

    private function notificarNuevaDonacion(int $idDonacion, int $idDonador, int $idCampana): void
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COALESCE(dm.razon_social, CONCAT(COALESCE(df.nombre, ''), ' ', COALESCE(df.apellido, ''))) AS donador,
                        c.nombre AS campana
                 FROM donadores d
                 LEFT JOIN donadores_fisicos df ON df.id_donador = d.id_donador
                 LEFT JOIN donadores_morales dm ON dm.id_donador = d.id_donador
                 INNER JOIN campanas c ON c.id_campana = ?
                 WHERE d.id_donador = ?"
            );
            $stmt->execute([$idCampana, $idDonador]);
            $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$detalle) {
                return;
            }

            $usuarios = $this->db->query(
                "SELECT id_usuario FROM usuarios
                 WHERE id_rol IN (SELECT id_rol FROM roles WHERE nombre IN ('Administrador', 'Coordinador'))"
            )->fetchAll(PDO::FETCH_COLUMN);
            foreach ($usuarios as $idUsuario) {
                try {
                    $this->notificaciones->crear([
                        'id_usuario' => (int)$idUsuario,
                        'tipo' => 'donacion',
                        'asunto' => 'Nueva donación registrada',
                        'mensaje' => 'Donación de ' . trim($detalle['donador']) . ' para la campaña ' . $detalle['campana'] . '.',
                        'canal' => 'web',
                        'id_tipo_ref' => 'donacion',
                        'id_referencia' => $idDonacion,
                    ]);
                } catch (Throwable $error) {
                    logger('No se pudo notificar nueva donación: ' . $error->getMessage());
                }
            }
        } catch (Throwable $error) {
            logger('No se pudo preparar notificación de nueva donación: ' . $error->getMessage());
        }
    }

    private function notificarDonacionVerificada(int $idDonacion): void
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT d.id_usuario_registrador, c.nombre AS campana
                 FROM donaciones d
                 INNER JOIN campanas c ON c.id_campana = d.id_campana
                 WHERE d.id_donacion = ?'
            );
            $stmt->execute([$idDonacion]);
            $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$detalle || empty($detalle['id_usuario_registrador'])) {
                return;
            }
            try {
                $this->notificaciones->crear([
                    'id_usuario' => (int)$detalle['id_usuario_registrador'],
                    'tipo' => 'donacion',
                    'asunto' => 'Tu donación fue verificada',
                    'mensaje' => 'La donación para la campaña ' . $detalle['campana'] . ' fue verificada y ya puede generarse una entrega con ella.',
                    'canal' => 'web',
                    'id_tipo_ref' => 'donacion',
                    'id_referencia' => $idDonacion,
                ]);
            } catch (Throwable $error) {
                logger('No se pudo notificar donación verificada: ' . $error->getMessage());
            }
        } catch (Throwable $error) {
            logger('No se pudo preparar notificación de donación verificada: ' . $error->getMessage());
        }
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
        if (!in_array($_SESSION['rol'] ?? '', $rolesPermitidos, true)) {
            $_SESSION['error_acceso'] = 'No tienes permiso para acceder a este módulo.';
            header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
            exit;
        }
    }

    private function verificarPermiso(string $accion): void
    {
        if (!usuarioPuede('donaciones', $accion)) {
            $this->redirigirConMensaje('No tienes permiso para realizar esta acción.', 'error');
        }
    }

    private function redirigirConMensaje(string $mensaje, string $tipo = 'success', string $url = ''): never
    {
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $tipo;
        header('Location: ' . ($url ?: BASE_URL . '/index.php?pagina=donaciones'));
        exit;
    }
}
