<?php
/**
 * models/EntregaModel.php
 * Acceso a datos de entregas.
 */
class EntregaModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function crear(array $datos): bool
    {
        try {
            $this->db->beginTransaction();
            $verificada = $this->db->prepare("SELECT id_donacion FROM donaciones WHERE id_donacion = ? AND estado = 'verificada' FOR UPDATE");
            $verificada->execute([$datos['id_donacion']]);
            if (!$verificada->fetchColumn()) {
                $this->db->rollBack();
                return false;
            }
            $beneficiario = $this->db->prepare("SELECT id_beneficiario FROM beneficiarios WHERE id_beneficiario = ? AND estado = 'activo' FOR UPDATE");
            $beneficiario->execute([$datos['id_beneficiario']]);
            if (!$beneficiario->fetchColumn()) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare(
                "INSERT INTO entregas
                 (id_donacion, id_beneficiario, id_usuario_responsable, cantidad_entregada,
                  fecha_entrega, evidencia_url, estado, observaciones)
                 VALUES (:donacion, :beneficiario, :usuario, :cantidad, :fecha, :evidencia, 'programada', :observaciones)"
            );
            $stmt->execute([
                ':donacion' => $datos['id_donacion'],
                ':beneficiario' => $datos['id_beneficiario'],
                ':usuario' => $datos['id_usuario_responsable'],
                ':cantidad' => $datos['cantidad_entregada'],
                ':fecha' => $datos['fecha_entrega'],
                ':evidencia' => $datos['evidencia_url'],
                ':observaciones' => $datos['observaciones'],
            ]);
            $this->db->commit();
            return true;
        } catch (PDOException $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger('EntregaModel::crear: ' . $error->getMessage());
            return false;
        }
    }

    public function listar(): array
    {
        return $this->db->query(
            "SELECT e.*, d.estado AS estado_donacion, d.fecha_recepcion, c.nombre AS campana,
                    COALESCE(dm.razon_social, CONCAT(COALESCE(df.nombre, ''), ' ', COALESCE(df.apellido, ''))) AS donador,
                    COALESCE(bm.razon_social, CONCAT(COALESCE(bf.nombre, ''), ' ', COALESCE(bf.apellido, ''))) AS beneficiario,
                    CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, '')) AS responsable,
                    COALESCE(de.unidad_medida, decon.moneda) AS unidad_medida,
                    COALESCE(tb.nombre, 'Donación económica') AS tipo_donacion
             FROM entregas e
             INNER JOIN donaciones d ON d.id_donacion = e.id_donacion
             INNER JOIN donadores dn ON dn.id_donador = d.id_donador
             LEFT JOIN donadores_fisicos df ON df.id_donador = dn.id_donador
             LEFT JOIN donadores_morales dm ON dm.id_donador = dn.id_donador
             INNER JOIN campanas c ON c.id_campana = d.id_campana
             INNER JOIN beneficiarios b ON b.id_beneficiario = e.id_beneficiario
             LEFT JOIN beneficiarios_fisicos bf ON bf.id_beneficiario = b.id_beneficiario
             LEFT JOIN beneficiarios_morales bm ON bm.id_beneficiario = b.id_beneficiario
             LEFT JOIN usuarios u ON u.id_usuario = e.id_usuario_responsable
             LEFT JOIN donaciones_especie de ON de.id_donacion = d.id_donacion
             LEFT JOIN tipos_bien tb ON tb.id_tipo_bien = de.id_tipo_bien
             LEFT JOIN donaciones_economicas decon ON decon.id_donacion = d.id_donacion
             ORDER BY e.id_entrega DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM entregas WHERE id_entrega = ? LIMIT 1');
        $stmt->execute([$id]);
        $entrega = $stmt->fetch(PDO::FETCH_ASSOC);
        return $entrega !== false ? $entrega : null;
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $actual = $this->obtenerPorId($id);
        $transiciones = [
            'programada' => ['en_proceso', 'completada', 'cancelada'],
            'en_proceso' => ['completada', 'cancelada'],
            'completada' => [],
            'cancelada' => [],
        ];
        if (!$actual || !in_array($estado, $transiciones[$actual['estado']] ?? [], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE entregas SET estado = ? WHERE id_entrega = ?');
        return $stmt->execute([$estado, $id]);
    }

    public function listarDonacionesVerificadas(): array
    {
        return $this->db->query(
            "SELECT d.id_donacion, d.fecha_recepcion,
                    COALESCE(dm.razon_social, CONCAT(COALESCE(df.nombre, ''), ' ', COALESCE(df.apellido, ''))) AS donador,
                    c.nombre AS campana, COALESCE(tb.nombre, 'Donación económica') AS tipo_donacion,
                    COALESCE(de.cantidad, decon.monto) AS cantidad,
                    COALESCE(de.unidad_medida, decon.moneda) AS unidad_medida
             FROM donaciones d
             INNER JOIN donadores dn ON dn.id_donador = d.id_donador
             LEFT JOIN donadores_fisicos df ON df.id_donador = dn.id_donador
             LEFT JOIN donadores_morales dm ON dm.id_donador = dn.id_donador
             INNER JOIN campanas c ON c.id_campana = d.id_campana
             LEFT JOIN donaciones_especie de ON de.id_donacion = d.id_donacion
             LEFT JOIN tipos_bien tb ON tb.id_tipo_bien = de.id_tipo_bien
             LEFT JOIN donaciones_economicas decon ON decon.id_donacion = d.id_donacion
             WHERE d.estado = 'verificada' ORDER BY d.id_donacion DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarBeneficiarios(): array
    {
        return $this->db->query(
            "SELECT b.id_beneficiario,
                    COALESCE(bm.razon_social, CONCAT(COALESCE(bf.nombre, ''), ' ', COALESCE(bf.apellido, ''))) AS nombre
             FROM beneficiarios b
             LEFT JOIN beneficiarios_fisicos bf ON bf.id_beneficiario = b.id_beneficiario
             LEFT JOIN beneficiarios_morales bm ON bm.id_beneficiario = b.id_beneficiario
             WHERE b.estado = 'activo' ORDER BY nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
