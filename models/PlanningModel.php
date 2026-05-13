<?php
/**
 * models/PlanningModel.php
 * Modelo para gestionar actividades y planning.
 */

class PlanningModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Obtiene actividades con filtros opcionales
     */
    public function getActividades(?string $zona = null, ?int $mes = null, ?int $anio = null): array
    {
        $query = "
            SELECT 
                id_actividad,
                titulo,
                descripcion,
                fecha_inicio,
                fecha_fin,
                zona,
                estado,
                id_usuario_responsable,
                created_at,
                u.nombre,
                u.apellido
            FROM actividades a
            LEFT JOIN usuarios u ON a.id_usuario_responsable = u.id_usuario
            WHERE 1=1
        ";

        $params = [];

        if (!empty($zona) && $zona !== 'todas') {
            if ($zona === 'ambas') {
                $query .= " AND zona IN ('san_martin', 'tlaxcala', 'ambas')";
            } else {
                $query .= " AND (zona = :zona OR zona = 'ambas')";
                $params[':zona'] = $zona;
            }
        }

        if (!empty($mes) && !empty($anio)) {
            $query .= " AND MONTH(fecha_inicio) = :mes AND YEAR(fecha_inicio) = :anio";
            $params[':mes'] = $mes;
            $params[':anio'] = $anio;
        }

        $query .= " ORDER BY fecha_inicio DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene actividades por mes para el calendario
     */
    public function getActividadesPorMes(int $mes, int $anio): array
    {
        $query = "
            SELECT 
                id_actividad,
                titulo,
                descripcion,
                fecha_inicio,
                fecha_fin,
                zona,
                estado,
                id_usuario_responsable,
                DAY(fecha_inicio) as dia,
                u.nombre,
                u.apellido
            FROM actividades a
            LEFT JOIN usuarios u ON a.id_usuario_responsable = u.id_usuario
            WHERE MONTH(fecha_inicio) = :mes AND YEAR(fecha_inicio) = :anio
            ORDER BY DAY(fecha_inicio) ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':mes' => $mes, ':anio' => $anio]);

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $dia = $row['dia'];
            if (!isset($result[$dia])) {
                $result[$dia] = [];
            }
            $result[$dia][] = $row;
        }

        return $result;
    }

    /**
     * Crea una nueva actividad
     */
    public function crearActividad(array $data): bool
    {
        $query = "
            INSERT INTO actividades 
            (titulo, descripcion, fecha_inicio, fecha_fin, zona, estado, id_usuario_responsable, created_at)
            VALUES (:titulo, :descripcion, :fecha_inicio, :fecha_fin, :zona, :estado, :id_usuario_responsable, :created_at)
        ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            ':titulo'                 => $data['titulo'] ?? '',
            ':descripcion'            => $data['descripcion'] ?? '',
            ':fecha_inicio'           => $data['fecha_inicio'] ?? null,
            ':fecha_fin'              => $data['fecha_fin'] ?? null,
            ':zona'                   => $data['zona'] ?? 'ambas',
            ':estado'                 => $data['estado'] ?? 'planeada',
            ':id_usuario_responsable' => $data['id_usuario_responsable'] ?? null,
            ':created_at'             => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Actualiza el estado de una actividad
     */
    public function actualizarEstado(int $id, string $estado): bool
    {
        $query = "UPDATE actividades SET estado = :estado WHERE id_actividad = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    /**
     * Obtiene KPIs de actividades
     */
    public function getKPIs(): array
    {
        $queries = [
            'total_actividades' => "SELECT COUNT(*) FROM actividades",
            'actividades_semana' => "
                SELECT COUNT(*) FROM actividades 
                WHERE DATE(fecha_inicio) >= DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY)
                AND DATE(fecha_inicio) <= DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)
            ",
            'san_martin' => "
                SELECT COUNT(*) FROM actividades 
                WHERE zona IN ('san_martin', 'ambas')
            ",
            'tlaxcala' => "
                SELECT COUNT(*) FROM actividades 
                WHERE zona IN ('tlaxcala', 'ambas')
            ",
        ];

        $kpis = [];
        foreach ($queries as $key => $sql) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $kpis[$key] = (int)$stmt->fetchColumn();
        }

        return $kpis;
    }

    /**
     * Obtiene una actividad por ID
     */
    public function getById(int $id): ?array
    {
        $query = "
            SELECT 
                a.*,
                u.nombre,
                u.apellido,
                u.email
            FROM actividades a
            LEFT JOIN usuarios u ON a.id_usuario_responsable = u.id_usuario
            WHERE a.id_actividad = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtiene todos los usuarios para asignar responsables
     */
    public function getUsuarios(): array
    {
        $query = "SELECT id_usuario, nombre, apellido, email FROM usuarios WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Elimina una actividad
     */
    public function eliminarActividad(int $id): bool
    {
        $query = "DELETE FROM actividades WHERE id_actividad = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
