<?php
/**
 * models/BeneficiarioModel.php
 * Modelo CRUD para la tabla beneficiarios.
 */
class BeneficiarioModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function consultar(): array
    {
        $sql = "
            SELECT
                b.id_beneficiario,
                b.nombre_completo,
                b.edad,
                c.nombre AS comunidad,
                b.direccion,
                b.telefono,
                b.estado,
                b.notas,
                CONCAT(u.nombre, ' ', u.apellido) AS registrado_por,
                b.created_at
            FROM beneficiarios b
            LEFT JOIN comunidades c ON b.id_comunidad = c.id_comunidad
            LEFT JOIN usuarios u ON b.id_usuario_registrador = u.id_usuario
            ORDER BY b.id_beneficiario DESC
        ";

        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function consultarPorId(int $id)
    {
        $sql = "
            SELECT
                b.id_beneficiario,
                b.nombre_completo,
                b.edad,
                b.id_comunidad,
                c.nombre AS comunidad,
                b.direccion,
                b.telefono,
                b.estado,
                b.notas,
                b.id_usuario_registrador,
                b.created_at,
                b.updated_at
            FROM beneficiarios b
            LEFT JOIN comunidades c ON b.id_comunidad = c.id_comunidad
            WHERE b.id_beneficiario = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTotal(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM beneficiarios');
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    public function obtenerTotalActivos(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM beneficiarios WHERE estado = 'activo'");
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    public function consultarComunidades(): array
    {
        $stmt = $this->db->query('SELECT id_comunidad, nombre FROM comunidades ORDER BY nombre ASC');
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function insertar(
        string $nombre_completo,
        ?int $edad,
        int $id_comunidad,
        string $direccion,
        string $telefono,
        string $estado,
        string $notas,
        int $id_usuario_registrador
    ): bool {
        $sql = "
            INSERT INTO beneficiarios
                (nombre_completo, edad, id_comunidad, direccion, telefono, estado, notas, id_usuario_registrador, created_at)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            trim($nombre_completo),
            $edad !== null ? $edad : null,
            $id_comunidad,
            trim($direccion),
            trim($telefono),
            $estado,
            trim($notas),
            $id_usuario_registrador,
        ]);
    }

    public function actualizar(
        int $id,
        string $nombre_completo,
        ?int $edad,
        int $id_comunidad,
        string $direccion,
        string $telefono,
        string $estado,
        string $notas
    ): bool {
        $sql = "
            UPDATE beneficiarios
            SET nombre_completo = ?,
                edad = ?,
                id_comunidad = ?,
                direccion = ?,
                telefono = ?,
                estado = ?,
                notas = ?,
                updated_at = NOW()
            WHERE id_beneficiario = ?
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            trim($nombre_completo),
            $edad !== null ? $edad : null,
            $id_comunidad,
            trim($direccion),
            trim($telefono),
            $estado,
            trim($notas),
            $id,
        ]);
    }

    public function eliminar(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar registros dependientes en beneficiario_tipos_apoyo
            $stmt = $this->db->prepare('DELETE FROM beneficiario_tipos_apoyo WHERE id_beneficiario = ?');
            $stmt->execute([$id]);

            // Eliminar registros dependientes en quejas_sugerencias
            $stmt = $this->db->prepare('DELETE FROM quejas_sugerencias WHERE id_benef_remitente = ?');
            $stmt->execute([$id]);

            // Eliminar beneficiario
            $stmt = $this->db->prepare('DELETE FROM beneficiarios WHERE id_beneficiario = ?');
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
