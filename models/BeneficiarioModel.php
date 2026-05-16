<?php
/**
 * models/BeneficiarioModel.php
 * Modelo CRUD para la tabla beneficiarios.
 */
class BeneficiarioModel
{
    private PDO $db;
    private ?string $ultimoError = null;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function getUltimoError(): ?string
    {
        return $this->ultimoError;
    }

    public function consultar(string $buscar = ''): array
    {
        $sql = "
            SELECT
                b.id_beneficiario,
                b.tipo_persona,
                CASE
                    WHEN bf.id_beneficiario IS NOT NULL THEN CONCAT(bf.nombre, ' ', bf.apellido)
                    WHEN bm.id_beneficiario IS NOT NULL THEN bm.razon_social
                    ELSE 'Desconocido'
                END AS nombre_completo,
                COALESCE(bf.edad, NULL) AS edad,
                COALESCE(bf.nombre, '') AS nombre,
                COALESCE(bf.apellido, '') AS apellido,
                COALESCE(bf.curp, '') AS curp,
                COALESCE(bf.fecha_nacimiento, '') AS fecha_nacimiento,
                COALESCE(bm.razon_social, '') AS razon_social,
                COALESCE(bm.rfc, '') AS rfc,
                c.nombre AS comunidad,
                b.id_comunidad,
                b.direccion,
                b.telefono,
                b.estado,
                b.id_usuario_registrador,
                CONCAT(u.nombre, ' ', u.apellido) AS registrado_por,
                b.created_at,
                b.updated_at
            FROM beneficiarios b
            LEFT JOIN beneficiarios_fisicos bf ON b.id_beneficiario = bf.id_beneficiario
            LEFT JOIN beneficiarios_morales bm ON b.id_beneficiario = bm.id_beneficiario
            LEFT JOIN comunidades c ON b.id_comunidad = c.id_comunidad
            LEFT JOIN usuarios u ON b.id_usuario_registrador = u.id_usuario
        ";

        $params = [];
        $buscar = trim((string)$buscar);
        if ($buscar !== '') {
            $sql .= "
            WHERE (
                LOWER(CONCAT(COALESCE(bf.nombre, ''), ' ', COALESCE(bf.apellido, ''))) LIKE ?
                OR LOWER(COALESCE(bm.razon_social, '')) LIKE ?
                OR LOWER(COALESCE(bf.curp, '')) LIKE ?
                OR LOWER(COALESCE(bm.rfc, '')) LIKE ?
                OR LOWER(COALESCE(c.nombre, '')) LIKE ?
                OR LOWER(COALESCE(b.telefono, '')) LIKE ?
                OR LOWER(COALESCE(b.estado, '')) LIKE ?
            )";
            $like = '%' . mb_strtolower($buscar, 'UTF-8') . '%';
            $params = [$like, $like, $like, $like, $like, $like, $like];
        }

        $sql .= "
            ORDER BY b.id_beneficiario DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function consultarPorId(int $id)
    {
        $sql = "
            SELECT
                b.id_beneficiario,
                b.tipo_persona,
                CASE
                    WHEN bf.id_beneficiario IS NOT NULL THEN CONCAT(bf.nombre, ' ', bf.apellido)
                    WHEN bm.id_beneficiario IS NOT NULL THEN bm.razon_social
                    ELSE 'Desconocido'
                END AS nombre_completo,
                COALESCE(bf.nombre, '') AS nombre,
                COALESCE(bf.apellido, '') AS apellido,
                COALESCE(bf.edad, NULL) AS edad,
                COALESCE(bf.curp, '') AS curp,
                COALESCE(bf.fecha_nacimiento, '') AS fecha_nacimiento,
                COALESCE(bm.razon_social, '') AS razon_social,
                COALESCE(bm.rfc, '') AS rfc,
                b.id_comunidad,
                c.nombre AS comunidad,
                b.direccion,
                b.telefono,
                b.estado,
                b.id_usuario_registrador,
                b.created_at,
                b.updated_at
            FROM beneficiarios b
            LEFT JOIN beneficiarios_fisicos bf ON b.id_beneficiario = bf.id_beneficiario
            LEFT JOIN beneficiarios_morales bm ON b.id_beneficiario = bm.id_beneficiario
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

    public function obtenerTotalPorTipo(string $tipo): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM beneficiarios WHERE tipo_persona = ?');
        $stmt->execute([$tipo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    public function consultarComunidades(): array
    {
        $stmt = $this->db->query('SELECT id_comunidad, nombre FROM comunidades ORDER BY nombre ASC');
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function insertar(
        string $tipoPersona,
        int $id_comunidad,
        string $direccion,
        string $telefono,
        string $estado,
        int $id_usuario_registrador,
        array $detalles
    ): bool {
        try {
            $this->db->beginTransaction();

            // Insertar en tabla beneficiarios
            $sql = "
                INSERT INTO beneficiarios
                    (tipo_persona, id_comunidad, direccion, telefono, estado, id_usuario_registrador, created_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, NOW())
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $tipoPersona,
                $id_comunidad,
                trim($direccion),
                trim($telefono),
                $estado,
                $id_usuario_registrador,
            ]);

            $idBeneficiario = (int) $this->db->lastInsertId();

            // Insertar datos específicos según tipo
            if ($tipoPersona === 'fisica') {
                $sql = "INSERT INTO beneficiarios_fisicos (id_beneficiario, nombre, apellido, edad, curp, fecha_nacimiento) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $idBeneficiario,
                    trim($detalles['nombre'] ?? ''),
                    trim($detalles['apellido'] ?? ''),
                    $detalles['edad'] ?? null,
                    trim($detalles['curp'] ?? '') ?: null,
                    trim($detalles['fecha_nacimiento'] ?? '') ?: null
                ]);
            } elseif ($tipoPersona === 'moral') {
                $sql = "INSERT INTO beneficiarios_morales (id_beneficiario, razon_social, rfc) 
                        VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $idBeneficiario,
                    trim($detalles['razon_social'] ?? ''),
                    trim($detalles['rfc'] ?? '') ?: null
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function actualizar(
        int $id,
        string $tipoPersona,
        int $id_comunidad,
        string $direccion,
        string $telefono,
        string $estado,
        array $detalles
    ): bool {
        try {
            $this->db->beginTransaction();

            // Actualizar tabla beneficiarios
            $sql = "
                UPDATE beneficiarios
                SET tipo_persona = ?,
                    id_comunidad = ?,
                    direccion = ?,
                    telefono = ?,
                    estado = ?,
                    updated_at = NOW()
                WHERE id_beneficiario = ?
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $tipoPersona,
                $id_comunidad,
                trim($direccion),
                trim($telefono),
                $estado,
                $id,
            ]);

            // Eliminar registros anteriores para evitar duplicados
            $this->db->prepare("DELETE FROM beneficiarios_fisicos WHERE id_beneficiario = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM beneficiarios_morales WHERE id_beneficiario = ?")->execute([$id]);

            // Insertar nuevos datos según tipo
            if ($tipoPersona === 'fisica') {
                $sql = "INSERT INTO beneficiarios_fisicos (id_beneficiario, nombre, apellido, edad, curp, fecha_nacimiento) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $id,
                    trim($detalles['nombre'] ?? ''),
                    trim($detalles['apellido'] ?? ''),
                    $detalles['edad'] ?? null,
                    trim($detalles['curp'] ?? '') ?: null,
                    trim($detalles['fecha_nacimiento'] ?? '') ?: null
                ]);
            } elseif ($tipoPersona === 'moral') {
                $sql = "INSERT INTO beneficiarios_morales (id_beneficiario, razon_social, rfc) 
                        VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $id,
                    trim($detalles['razon_social'] ?? ''),
                    trim($detalles['rfc'] ?? '') ?: null
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    public function eliminar(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar asignaciones de voluntarios relacionadas con entregas del beneficiario
            $stmt = $this->db->prepare(
                'DELETE FROM asignaciones_voluntario
                 WHERE id_entrega IN (
                     SELECT id_entrega FROM entregas WHERE id_beneficiario = ?
                 )'
            );
            $stmt->execute([$id]);

            // Eliminar entregas asociadas al beneficiario antes de borrar el registro principal
            $stmt = $this->db->prepare('DELETE FROM entregas WHERE id_beneficiario = ?');
            $stmt->execute([$id]);

            // Eliminar registros dependientes en beneficiario_tipos_apoyo
            $stmt = $this->db->prepare('DELETE FROM beneficiario_tipos_apoyo WHERE id_beneficiario = ?');
            $stmt->execute([$id]);

            // Eliminar registros dependientes en quejas_sugerencias si existe la tabla
            if ($this->tablaExiste('quejas_sugerencias')) {
                $stmt = $this->db->prepare('DELETE FROM quejas_sugerencias WHERE id_benef_remitente = ?');
                $stmt->execute([$id]);
            }

            // Asegurar eliminación de datos de tipo específico si la base de datos no aplica ON DELETE CASCADE
            $stmt = $this->db->prepare('DELETE FROM beneficiarios_fisicos WHERE id_beneficiario = ?');
            $stmt->execute([$id]);
            $stmt = $this->db->prepare('DELETE FROM beneficiarios_morales WHERE id_beneficiario = ?');
            $stmt->execute([$id]);

            // Eliminar beneficiario
            $stmt = $this->db->prepare('DELETE FROM beneficiarios WHERE id_beneficiario = ?');
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->ultimoError = $e->getMessage();
            return false;
        }
    }

    private function tablaExiste(string $nombre): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?"
        );
        $stmt->execute([$nombre]);
        return (bool) $stmt->fetchColumn();
    }
}
