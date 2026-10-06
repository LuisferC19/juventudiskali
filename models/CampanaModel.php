<?php
/**
 * models/CampanaModel.php
 * Acceso a datos de campañas.
 */
class CampanaModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function crear(array $datos): bool
    {
        $sql = 'INSERT INTO campanas
                (id_usuario_creador, nombre, descripcion, tipo_meta, meta_economica,
                 fecha_inicio, fecha_cierre, estado, imagen_url)
                VALUES (:usuario, :nombre, :descripcion, :tipo, :meta, :inicio, :cierre, :estado, :imagen)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':usuario' => $datos['id_usuario_creador'],
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':tipo' => $datos['tipo_meta'],
            ':meta' => $datos['meta_economica'],
            ':inicio' => $datos['fecha_inicio'],
            ':cierre' => $datos['fecha_cierre'],
            ':estado' => $datos['estado'],
            ':imagen' => $datos['imagen_url'],
        ]);
    }

    public function listar(): array
    {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) AS creador
                FROM campanas c
                LEFT JOIN usuarios u ON u.id_usuario = c.id_usuario_creador
                ORDER BY c.id_campana DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM campanas WHERE id_campana = ? LIMIT 1');
        $stmt->execute([$id]);
        $campana = $stmt->fetch(PDO::FETCH_ASSOC);

        return $campana !== false ? $campana : null;
    }

    public function actualizar(int $id, array $datos): bool
    {
        if (!$this->obtenerPorId($id)) {
            return false;
        }
        $stmt = $this->db->prepare(
            'UPDATE campanas
             SET nombre = :nombre, descripcion = :descripcion, tipo_meta = :tipo,
                 meta_economica = :meta, fecha_inicio = :inicio, fecha_cierre = :cierre,
                 estado = :estado, imagen_url = :imagen
             WHERE id_campana = :id'
        );

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':tipo' => $datos['tipo_meta'],
            ':meta' => $datos['meta_economica'],
            ':inicio' => $datos['fecha_inicio'],
            ':cierre' => $datos['fecha_cierre'],
            ':estado' => $datos['estado'],
            ':imagen' => $datos['imagen_url'],
            ':id' => $id,
        ]);
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        if (!$this->obtenerPorId($id)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE campanas SET estado = ? WHERE id_campana = ?');
        return $stmt->execute([$estado, $id]);
    }
}
