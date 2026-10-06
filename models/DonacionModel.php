<?php
/**
 * models/DonacionModel.php
 * Acceso a datos transaccional de donaciones y sus subtipos.
 */
class DonacionModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function crear(array $datos): bool
    {
        if (!in_array($datos['tipo'] ?? '', ['especie', 'economica'], true)) {
            return false;
        }

        try {
            $this->db->beginTransaction();
            $donador = $this->db->prepare('SELECT id_donador FROM donadores WHERE id_donador = ? AND activo = 1 FOR UPDATE');
            $donador->execute([$datos['id_donador']]);
            $campana = $this->db->prepare("SELECT id_campana FROM campanas WHERE id_campana = ? AND estado = 'activa' FOR UPDATE");
            $campana->execute([$datos['id_campana']]);
            if (!$donador->fetchColumn() || !$campana->fetchColumn()) {
                $this->db->rollBack();
                return false;
            }
            $stmt = $this->db->prepare(
                "INSERT INTO donaciones
                 (id_donador, id_campana, id_usuario_registrador, fecha_recepcion, estado, evidencia_url)
                 VALUES (:donador, :campana, :usuario, :fecha, 'pendiente', :evidencia)"
            );
            $stmt->execute([
                ':donador' => $datos['id_donador'],
                ':campana' => $datos['id_campana'],
                ':usuario' => $datos['id_usuario_registrador'],
                ':fecha' => $datos['fecha_recepcion'],
                ':evidencia' => $datos['evidencia_url'],
            ]);
            $id = (int)$this->db->lastInsertId();

            if ($datos['tipo'] === 'especie') {
                $bien = $this->db->prepare('SELECT unidad_medida FROM tipos_bien WHERE id_tipo_bien = ? AND activo = 1');
                $bien->execute([$datos['id_tipo_bien']]);
                $unidad = $bien->fetchColumn();
                if ($unidad === false) {
                    $this->db->rollBack();
                    return false;
                }
                $subtipo = $this->db->prepare(
                    'INSERT INTO donaciones_especie
                     (id_donacion, id_tipo_bien, descripcion, cantidad, unidad_medida)
                     VALUES (:id, :bien, :descripcion, :cantidad, :unidad)'
                );
                $subtipo->execute([
                    ':id' => $id,
                    ':bien' => $datos['id_tipo_bien'],
                    ':descripcion' => $datos['descripcion'],
                    ':cantidad' => $datos['cantidad'],
                    ':unidad' => $unidad,
                ]);
            } else {
                $subtipo = $this->db->prepare(
                    'INSERT INTO donaciones_economicas
                     (id_donacion, monto, moneda, metodo_pago, referencia_pago)
                     VALUES (:id, :monto, :moneda, :metodo, :referencia)'
                );
                $subtipo->execute([
                    ':id' => $id,
                    ':monto' => $datos['monto'],
                    ':moneda' => $datos['moneda'],
                    ':metodo' => $datos['metodo_pago'],
                    ':referencia' => $datos['referencia_pago'],
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger('DonacionModel::crear: ' . $error->getMessage());
            return false;
        }
    }

    public function listar(?string $estado = null, ?int $idCampana = null): array
    {
        $sql = "SELECT d.id_donacion, d.fecha_recepcion, d.estado, d.evidencia_url,
                       d.id_campana, c.nombre AS campana, d.id_donador,
                       COALESCE(dm.razon_social, CONCAT(COALESCE(df.nombre, ''), ' ', COALESCE(df.apellido, ''))) AS donador,
                       CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, '')) AS registrador,
                       CASE WHEN de.id_donacion IS NOT NULL THEN 'especie' ELSE 'economica' END AS tipo,
                       COALESCE(tb.nombre, 'Donación económica') AS detalle, de.descripcion,
                       COALESCE(de.cantidad, decon.monto) AS cantidad_monto,
                       COALESCE(de.unidad_medida, decon.moneda) AS unidad_medida,
                       decon.metodo_pago
                FROM donaciones d
                INNER JOIN donadores dn ON dn.id_donador = d.id_donador
                LEFT JOIN donadores_fisicos df ON df.id_donador = dn.id_donador
                LEFT JOIN donadores_morales dm ON dm.id_donador = dn.id_donador
                INNER JOIN campanas c ON c.id_campana = d.id_campana
                LEFT JOIN usuarios u ON u.id_usuario = d.id_usuario_registrador
                LEFT JOIN donaciones_especie de ON de.id_donacion = d.id_donacion
                LEFT JOIN tipos_bien tb ON tb.id_tipo_bien = de.id_tipo_bien
                LEFT JOIN donaciones_economicas decon ON decon.id_donacion = d.id_donacion
                WHERE 1 = 1";
        $params = [];
        if ($estado !== null && $estado !== '') {
            $sql .= ' AND d.estado = ?';
            $params[] = $estado;
        }
        if ($idCampana !== null) {
            $sql .= ' AND d.id_campana = ?';
            $params[] = $idCampana;
        }
        $sql .= ' ORDER BY d.id_donacion DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, de.id_tipo_bien, de.descripcion, de.cantidad, de.unidad_medida,
                    decon.monto, decon.moneda, decon.metodo_pago, decon.referencia_pago,
                    CASE WHEN de.id_donacion IS NOT NULL THEN 'especie' ELSE 'economica' END AS tipo
             FROM donaciones d
             LEFT JOIN donaciones_especie de ON de.id_donacion = d.id_donacion
             LEFT JOIN donaciones_economicas decon ON decon.id_donacion = d.id_donacion
             WHERE d.id_donacion = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $donacion = $stmt->fetch(PDO::FETCH_ASSOC);
        return $donacion !== false ? $donacion : null;
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $actual = $this->obtenerPorId($id);
        $transiciones = [
            'pendiente' => ['recibida', 'rechazada'],
            'recibida' => ['verificada', 'rechazada'],
            'verificada' => [],
            'rechazada' => [],
        ];
        if (!$actual || !in_array($estado, $transiciones[$actual['estado']] ?? [], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE donaciones SET estado = ? WHERE id_donacion = ?');
        return $stmt->execute([$estado, $id]);
    }

    public function listarDonadores(): array
    {
        $stmt = $this->db->query(
            "SELECT d.id_donador,
                    COALESCE(dm.razon_social, CONCAT(COALESCE(df.nombre, ''), ' ', COALESCE(df.apellido, ''))) AS nombre
             FROM donadores d
             LEFT JOIN donadores_fisicos df ON df.id_donador = d.id_donador
             LEFT JOIN donadores_morales dm ON dm.id_donador = d.id_donador
             WHERE d.activo = 1 ORDER BY nombre"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCampanasActivas(): array
    {
        return $this->db->query("SELECT id_campana, nombre FROM campanas WHERE estado = 'activa' ORDER BY nombre")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCampanas(): array
    {
        return $this->db->query('SELECT id_campana, nombre FROM campanas ORDER BY nombre')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTiposBien(): array
    {
        return $this->db->query('SELECT id_tipo_bien, nombre, unidad_medida FROM tipos_bien WHERE activo = 1 ORDER BY nombre')
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}