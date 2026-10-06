<?php
/**
 * controllers/EntregasController.php
 * Registro y seguimiento de entregas.
 */
require_once 'models/EntregaModel.php';
require_once 'models/NotificacionModel.php';

class EntregasController
{
    private PDO $db;
    private EntregaModel $modelo;
    private NotificacionModel $notificaciones;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
        $this->modelo = new EntregaModel($conexion);
        $this->notificaciones = new NotificacionModel($conexion);
    }

    public function index(): void
    {
        $this->verificarSesion();
        $this->verificarRol(['Administrador', 'Coordinador', 'Voluntario', 'Auditor', 'Captador', 'Analista', 'Legal']);
        if (!usuarioPuede('entregas', 'ver')) {
            $this->redirigirConMensaje('No tienes permiso para ver entregas.', 'error', BASE_URL . '/index.php?pagina=dashboard');
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
                $this->redirigirConMensaje('Acción de entrega inválida.', 'error');
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
        $entregas = $this->modelo->listar();
        $total = count($entregas);
        $total_programadas = count(array_filter($entregas, static fn(array $e): bool => $e['estado'] === 'programada'));
        $total_en_proceso = count(array_filter($entregas, static fn(array $e): bool => $e['estado'] === 'en_proceso'));
        $mensaje = $_SESSION['mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'success';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
        $pagina_activa = 'entregas';
        $titulo_pagina = 'Entregas';
        $conexion = $this->db;
        require 'views/pages/EntregasView.php';
    }

    private function mostrarFormularioNuevo(): void
    {
        $donaciones = $this->modelo->listarDonacionesVerificadas();
        $beneficiarios = $this->modelo->listarBeneficiarios();
        $pagina_activa = 'entregas';
        $titulo_pagina = 'Nueva entrega';
        require 'views/pages/Entregas_FormView.php';
    }

    private function guardarNuevo(): void
    {
        csrfVerify();
        $idDonacion = sanitizeInt($_POST['id_donacion'] ?? null);
        $idBeneficiario = sanitizeInt($_POST['id_beneficiario'] ?? null);
        $cantidad = trim((string)($_POST['cantidad_entregada'] ?? ''));
        $fecha = trim((string)($_POST['fecha_entrega'] ?? ''));
        $evidencia = trim((string)($_POST['evidencia_url'] ?? ''));
        $dateValid = (bool)preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $fecha)
            && DateTime::createFromFormat('!Y-m-d\TH:i', $fecha)?->format('Y-m-d\TH:i') === $fecha;
        if ($idDonacion === null || $idBeneficiario === null || !is_numeric($cantidad)
            || (float)$cantidad <= 0 || !$dateValid
            || ($evidencia !== '' && filter_var($evidencia, FILTER_VALIDATE_URL) === false)) {
            $this->redirigirConMensaje('Revisa los datos requeridos de la entrega.', 'error', BASE_URL . '/index.php?pagina=entregas&accion=crear');
        }
        $datos = [
            'id_donacion' => $idDonacion,
            'id_beneficiario' => $idBeneficiario,
            'id_usuario_responsable' => (int)$_SESSION['id_usuario'],
            'cantidad_entregada' => $cantidad,
            'fecha_entrega' => str_replace('T', ' ', $fecha) . ':00',
            'evidencia_url' => $evidencia !== '' ? $evidencia : null,
            'observaciones' => trim((string)($_POST['observaciones'] ?? '')),
        ];
        $ok = $this->modelo->crear($datos);
        $this->redirigirConMensaje($ok ? 'Entrega registrada correctamente.' : 'No se pudo registrar: la donación debe estar verificada.', $ok ? 'success' : 'error');
    }

    private function cambiarEstado(int $id): void
    {
        csrfVerify();
        $estado = trim((string)($_POST['estado'] ?? ''));
        $ok = $this->modelo->cambiarEstado($id, $estado);
        if ($ok && $estado === 'completada') {
            $this->notificarEntregaCompletada($id);
        }
        $this->redirigirConMensaje($ok ? 'Estado de entrega actualizado.' : 'Estado inválido o entrega inexistente.', $ok ? 'success' : 'error');
    }

    private function notificarEntregaCompletada(int $idEntrega): void
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COALESCE(bm.razon_social, CONCAT(COALESCE(bf.nombre, ''), ' ', COALESCE(bf.apellido, ''))) AS beneficiario,
                        e.cantidad_entregada, e.id_donacion
                 FROM entregas e
                 INNER JOIN beneficiarios b ON b.id_beneficiario = e.id_beneficiario
                 LEFT JOIN beneficiarios_fisicos bf ON bf.id_beneficiario = b.id_beneficiario
                 LEFT JOIN beneficiarios_morales bm ON bm.id_beneficiario = b.id_beneficiario
                 WHERE e.id_entrega = ?"
            );
            $stmt->execute([$idEntrega]);
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
                        'tipo' => 'entrega',
                        'asunto' => 'Entrega completada',
                        'mensaje' => 'Entrega a ' . trim($detalle['beneficiario']) . ' completada. Cantidad entregada: ' . $detalle['cantidad_entregada'] . '.',
                        'canal' => 'web',
                        'id_tipo_ref' => 'entrega',
                        'id_referencia' => $idEntrega,
                    ]);
                } catch (Throwable $error) {
                    logger('No se pudo notificar entrega completada: ' . $error->getMessage());
                }
            }
        } catch (Throwable $error) {
            logger('No se pudo preparar notificación de entrega completada: ' . $error->getMessage());
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
        if (!usuarioPuede('entregas', $accion)) {
            $this->redirigirConMensaje('No tienes permiso para realizar esta acción.', 'error');
        }
    }

    private function redirigirConMensaje(string $mensaje, string $tipo = 'success', string $url = ''): never
    {
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $tipo;
        header('Location: ' . ($url ?: BASE_URL . '/index.php?pagina=entregas'));
        exit;
    }
}
