<?php
/**
 * models/DonadoresModel.php
 * Modelo para gestionar donadores en la base de datos
 */
class DonadoresModel {
    private PDO $db;

    public function __construct(PDO $conexion) {
        $this->db = $conexion;
    }

    /**
     * Obtener todos los donadores con sus detalles
     */
    public function obtenerTodos(): array {
        $sql = "
            SELECT 
                d.id_donador,
                CASE
                    WHEN df.id_donador IS NOT NULL THEN 'Física'
                    WHEN dm.id_donador IS NOT NULL THEN 'Moral'
                    ELSE 'Desconocido'
                END AS tipo_persona,
                COALESCE(dm.razon_social, CONCAT(df.nombre, ' ', df.apellido)) AS nombre_completo,
                COALESCE(df.nombre, '') AS nombre,
                COALESCE(df.apellido, '') AS apellido,
                COALESCE(dm.razon_social, '') AS razon_social,
                d.email,
                d.telefono,
                d.puntos_acumulados,
                COALESCE(ng.nombre, 'Sin nivel') AS nivel,
                d.activo,
                d.created_at
            FROM donadores d
            LEFT JOIN donadores_fisicos df ON d.id_donador = df.id_donador
            LEFT JOIN donadores_morales dm ON d.id_donador = dm.id_donador
            LEFT JOIN niveles_gamificacion ng ON d.id_nivel = ng.id_nivel
            ORDER BY d.id_donador DESC
        ";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Obtener un donador específico por ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "
            SELECT 
                d.id_donador,
                CASE
                    WHEN df.id_donador IS NOT NULL THEN 'fisica'
                    WHEN dm.id_donador IS NOT NULL THEN 'moral'
                    ELSE 'desconocido'
                END AS tipo_persona,
                COALESCE(df.nombre, '') AS nombre,
                COALESCE(df.apellido, '') AS apellido,
                COALESCE(df.curp, '') AS curp,
                COALESCE(df.fecha_nacimiento, '') AS fecha_nacimiento,
                COALESCE(dm.razon_social, '') AS razon_social,
                COALESCE(dm.rfc, '') AS rfc,
                COALESCE(dm.representante_legal, '') AS representante_legal,
                COALESCE(dm.giro_comercial, '') AS giro_comercial,
                d.email,
                d.telefono,
                d.puntos_acumulados,
                d.id_nivel,
                d.activo
            FROM donadores d
            LEFT JOIN donadores_fisicos df ON d.id_donador = df.id_donador
            LEFT JOIN donadores_morales dm ON d.id_donador = dm.id_donador
            WHERE d.id_donador = ?
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $resultado = $stmt->fetch();
        
        return $resultado !== false ? $resultado : null;
    }

    /**
     * Crear un nuevo donador (persona física o moral)
     */
    public function crear(string $tipoPersona, string $email, ?string $telefono, int $puntos, bool $activo, array $detalles): bool {
        try {
            $this->db->beginTransaction();

            // Insertar en tabla donadores
            $sql = "INSERT INTO donadores (email, telefono, puntos_acumulados, activo, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $telefono, $puntos, $activo ? 1 : 0]);
            
            $idDonador = (int) $this->db->lastInsertId();

            // Insertar datos específicos según tipo
            if ($tipoPersona === 'fisica') {
                $sql = "INSERT INTO donadores_fisicos (id_donador, nombre, apellido, curp, fecha_nacimiento) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $idDonador,
                    trim($detalles['nombre'] ?? ''),
                    trim($detalles['apellido'] ?? ''),
                    trim($detalles['curp'] ?? '') ?: null,
                    trim($detalles['fecha_nacimiento'] ?? '') ?: null
                ]);
            } elseif ($tipoPersona === 'moral') {
                $sql = "INSERT INTO donadores_morales (id_donador, razon_social, rfc, representante_legal, giro_comercial) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $idDonador,
                    trim($detalles['razon_social'] ?? ''),
                    trim($detalles['rfc'] ?? '') ?: null,
                    trim($detalles['representante_legal'] ?? '') ?: null,
                    trim($detalles['giro_comercial'] ?? '') ?: null
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Actualizar un donador
     */
    public function actualizar(int $id, string $tipoPersona, string $email, ?string $telefono, int $puntos, bool $activo, array $detalles): bool {
        try {
            $this->db->beginTransaction();

            // Actualizar tabla donadores
            $sql = "UPDATE donadores SET email = ?, telefono = ?, puntos_acumulados = ?, activo = ? WHERE id_donador = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $telefono, $puntos, $activo ? 1 : 0, $id]);

            // Eliminar registros anteriores para evitar duplicados
            $this->db->prepare("DELETE FROM donadores_fisicos WHERE id_donador = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM donadores_morales WHERE id_donador = ?")->execute([$id]);

            // Insertar nuevos datos según tipo
            if ($tipoPersona === 'fisica') {
                $sql = "INSERT INTO donadores_fisicos (id_donador, nombre, apellido, curp, fecha_nacimiento) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $id,
                    trim($detalles['nombre'] ?? ''),
                    trim($detalles['apellido'] ?? ''),
                    trim($detalles['curp'] ?? '') ?: null,
                    trim($detalles['fecha_nacimiento'] ?? '') ?: null
                ]);
            } elseif ($tipoPersona === 'moral') {
                $sql = "INSERT INTO donadores_morales (id_donador, razon_social, rfc, representante_legal, giro_comercial) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $id,
                    trim($detalles['razon_social'] ?? ''),
                    trim($detalles['rfc'] ?? '') ?: null,
                    trim($detalles['representante_legal'] ?? '') ?: null,
                    trim($detalles['giro_comercial'] ?? '') ?: null
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Eliminar un donador
     */
    public function eliminar(int $id): bool {
        try {
            $this->db->beginTransaction();
            
            // Eliminar registros relacionados (ON DELETE CASCADE debería hacerlo automáticamente)
            $this->db->prepare("DELETE FROM donadores_fisicos WHERE id_donador = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM donadores_morales WHERE id_donador = ?")->execute([$id]);
            
            // Eliminar donador
            $sql = "DELETE FROM donadores WHERE id_donador = ?";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([$id]);
            
            $this->db->commit();
            return $resultado;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
