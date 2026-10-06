<?php
/**
 * controllers/CampanasController.php
 * CRUD mínimo para campañas.
 */
require_once 'models/CampanaModel.php';
require_once 'models/NotificacionModel.php';

class CampanasController
{
    private PDO $db;
    private CampanaModel $modelo;
    private NotificacionModel $notificaciones;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
        $this->modelo = new CampanaModel($conexion);
        $this->notificaciones = new NotificacionModel($conexion);
    }

    public function index(): void
    {
        $this->verificarSesion();
        $this->verificarRol(['Administrador', 'Coordinador', 'Auditor', 'Comunicación', 'Captador', 'Analista', 'Legal', 'Externo']);
        $accion = trim((string)($_GET['accion'] ?? 'listar'));
        $id = sanitizeInt($_GET['id'] ?? null);

        if (!usuarioPuede('campanas', 'ver')) {
            $this->redirigirConMensaje('No tienes permiso para ver campañas.', 'error', BASE_URL . '/index.php?pagina=dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($accion === 'guardar') {
                if (isset($_GET['id']) && !$id) {
                    $this->redirigirConMensaje('Identificador de campaña inválido.', 'error');
                }
                $this->verificarPermiso($id ? 'editar' : 'crear');
                $id ? $this->guardarEdicion($id) : $this->guardarNuevo();
            } elseif ($accion === 'cambiar-estado' && $id) {
                $this->verificarPermiso('editar');
                $this->cambiarEstado($id);
            } else {
                $this->redirigirConMensaje('Acción de campaña inválida.', 'error');
            }
            return;
        }

        if ($accion === 'crear') {
            $this->verificarPermiso('crear');
            $this->mostrarFormularioNuevo();
            return;
        }
        if ($accion === 'editar' && $id) {
            $this->verificarPermiso('editar');
            $this->mostrarFormularioEdicion($id);
            return;
        }
        $this->listar();
    }

    private function listar(): void
    {
        $campanas = $this->modelo->listar();
        $total = count($campanas);
        $total_activas = count(array_filter($campanas, static fn(array $c): bool => $c['estado'] === 'activa'));
        $total_cerradas = count(array_filter($campanas, static fn(array $c): bool => $c['estado'] === 'cerrada'));
        $mensaje = $_SESSION['mensaje'] ?? null;
        $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'success';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
        $pagina_activa = 'campanas';
        $titulo_pagina = 'Campañas';
        $conexion = $this->db;
        require 'views/pages/CampanasView.php';
    }

    private function mostrarFormularioNuevo(): void
    {
        $campana = null;
        $pagina_activa = 'campanas';
        $titulo_pagina = 'Nueva campaña';
        require 'views/pages/Campanas_FormView.php';
    }

    private function mostrarFormularioEdicion(int $id): void
    {
        $campana = $this->modelo->obtenerPorId($id);
        if (!$campana) {
            $this->redirigirConMensaje('Campaña no encontrada.', 'error');
        }
        $pagina_activa = 'campanas';
        $titulo_pagina = 'Editar campaña';
        require 'views/pages/Campanas_FormView.php';
    }

    private function guardarNuevo(): void
    {
        csrfVerify();
        $datos = $this->leerDatos();
        if (!$this->datosValidos($datos)) {
            $this->redirigirConMensaje('Revisa los datos de la campaña y las fechas ingresadas.', 'error', BASE_URL . '/index.php?pagina=campanas&accion=crear');
        }
        $datos['id_usuario_creador'] = (int)$_SESSION['id_usuario'];
        $ok = $this->modelo->crear($datos);
        if ($ok && $datos['estado'] === 'activa') {
            $this->notificarCampanaActiva((int)$this->db->lastInsertId(), $datos['nombre'], $datos['fecha_cierre']);
        }
        $this->redirigirConMensaje($ok ? 'Campaña creada correctamente.' : 'No se pudo crear la campaña.', $ok ? 'success' : 'error');
    }

    private function guardarEdicion(int $id): void
    {
        csrfVerify();
        $datos = $this->leerDatos();
        if (!$this->datosValidos($datos)) {
            $this->redirigirConMensaje('Revisa los datos de la campaña y las fechas ingresadas.', 'error', BASE_URL . '/index.php?pagina=campanas&accion=editar&id=' . $id);
        }
        $anterior = $this->modelo->obtenerPorId($id);
        $ok = $this->modelo->actualizar($id, $datos);
        if ($ok && $anterior && $anterior['estado'] !== 'activa' && $datos['estado'] === 'activa') {
            $this->notificarCampanaActiva($id, $datos['nombre'], $datos['fecha_cierre']);
        }
        $this->redirigirConMensaje($ok ? 'Campaña actualizada correctamente.' : 'No se pudo actualizar la campaña.', $ok ? 'success' : 'error');
    }

    private function cambiarEstado(int $id): void
    {
        csrfVerify();
        $estado = trim((string)($_POST['estado'] ?? ''));
        if (!in_array($estado, ['borrador', 'activa', 'pausada', 'cerrada', 'cancelada'], true)) {
            $this->redirigirConMensaje('Estado de campaña inválido.', 'error');
        }
        $anterior = $this->modelo->obtenerPorId($id);
        $ok = $this->modelo->cambiarEstado($id, $estado);
        if ($ok && $anterior && $anterior['estado'] !== 'activa' && $estado === 'activa') {
            $this->notificarCampanaActiva($id, $anterior['nombre'], $anterior['fecha_cierre']);
        }
        $this->redirigirConMensaje($ok ? 'Estado de campaña actualizado.' : 'No se pudo actualizar el estado.', $ok ? 'success' : 'error');
    }

    private function notificarCampanaActiva(int $idCampana, string $nombre, string $fechaCierre): void
    {
        try {
            $usuarios = $this->db->query('SELECT id_usuario FROM usuarios WHERE activo = 1')
                ->fetchAll(PDO::FETCH_COLUMN);
            foreach ($usuarios as $idUsuario) {
                try {
                    $this->notificaciones->crear([
                        'id_usuario' => (int)$idUsuario,
                        'tipo' => 'campana',
                        'asunto' => 'Nueva campaña activa',
                        'mensaje' => 'La campaña ' . $nombre . ' está activa. Fecha de cierre: ' . $fechaCierre . '.',
                        'canal' => 'web',
                        'id_tipo_ref' => 'campana',
                        'id_referencia' => $idCampana,
                    ]);
                } catch (Throwable $error) {
                    logger('No se pudo notificar campaña activa: ' . $error->getMessage());
                }
            }
        } catch (Throwable $error) {
            logger('No se pudo preparar notificación de campaña activa: ' . $error->getMessage());
        }
    }

    private function leerDatos(): array
    {
        $metaEconomica = trim((string)($_POST['meta_economica'] ?? ''));
        return [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
            'tipo_meta' => trim((string)($_POST['tipo_meta'] ?? '')),
            'meta_economica' => $metaEconomica === '' ? '0' : $metaEconomica,
            'fecha_inicio' => trim((string)($_POST['fecha_inicio'] ?? '')),
            'fecha_cierre' => trim((string)($_POST['fecha_cierre'] ?? '')),
            'estado' => trim((string)($_POST['estado'] ?? 'borrador')),
            'imagen_url' => trim((string)($_POST['imagen_url'] ?? '')) ?: null,
        ];
    }

    private function datosValidos(array $datos): bool
    {
        $formatDate = static fn(string $date): bool => (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
            && DateTime::createFromFormat('!Y-m-d', $date)?->format('Y-m-d') === $date;
        return $datos['nombre'] !== ''
            && in_array($datos['tipo_meta'], ['economica', 'especie', 'mixta', 'servicio'], true)
            && is_numeric($datos['meta_economica'])
            && (float)$datos['meta_economica'] >= 0
            && $formatDate($datos['fecha_inicio'])
            && $formatDate($datos['fecha_cierre'])
            && $datos['fecha_cierre'] >= $datos['fecha_inicio']
            && in_array($datos['estado'], ['borrador', 'activa', 'pausada', 'cerrada', 'cancelada'], true)
            && ($datos['imagen_url'] === null || filter_var($datos['imagen_url'], FILTER_VALIDATE_URL) !== false);
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
        if (!usuarioPuede('campanas', $accion)) {
            $this->redirigirConMensaje('No tienes permiso para realizar esta acción.', 'error');
        }
    }

    private function redirigirConMensaje(string $mensaje, string $tipo = 'success', string $url = ''): never
    {
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $tipo;
        header('Location: ' . ($url ?: BASE_URL . '/index.php?pagina=campanas'));
        exit;
    }
}
