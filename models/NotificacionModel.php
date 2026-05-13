<?php
/**
 * models/NotificacionModel.php
 * Modelo para gestionar notificaciones del sistema.
 */

class NotificacionModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Obtiene notificaciones del usuario
     */
    public function getNotificaciones(
        int $id_usuario,
        string $canal = 'web',
        bool $solo_no_leidas = false
    ): array {
        $query = "
            SELECT 
                id_notificacion,
                tipo,
                asunto,
                mensaje,
                canal,
                leida,
                id_tipo_ref,
                id_referencia,
                fecha_creacion
            FROM notificaciones
            WHERE id_usuario = :id_usuario
        ";

        $params = [':id_usuario' => $id_usuario];

        // Filtrar por canal (web)
        if (!empty($canal)) {
            $query .= " AND FIND_IN_SET(:canal, canal) > 0";
            $params[':canal'] = $canal;
        }

        // Solo no leídas
        if ($solo_no_leidas) {
            $query .= " AND leida = 0";
        }

        $query .= " ORDER BY fecha_creacion DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Cuenta notificaciones no leídas
     */
    public function contarNoLeidas(int $id_usuario, string $canal = 'web'): int
    {
        $query = "
            SELECT COUNT(*) 
            FROM notificaciones 
            WHERE id_usuario = :id_usuario 
              AND leida = 0
              AND FIND_IN_SET(:canal, canal) > 0
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':canal'      => $canal,
        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Marca una notificación como leída
     */
    public function marcarLeida(int $id_notificacion): bool
    {
        $query = "UPDATE notificaciones SET leida = 1 WHERE id_notificacion = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id_notificacion]);
    }

    /**
     * Marca todas las notificaciones del usuario como leídas
     */
    public function marcarTodasLeidas(int $id_usuario): bool
    {
        $query = "UPDATE notificaciones SET leida = 1 WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id_usuario' => $id_usuario]);
    }

    /**
     * Crea una nueva notificación
     */
    public function crear(array $data): bool
    {
        $query = "
            INSERT INTO notificaciones 
            (id_usuario, tipo, asunto, mensaje, canal, leida, id_tipo_ref, id_referencia, fecha_creacion)
            VALUES (:id_usuario, :tipo, :asunto, :mensaje, :canal, :leida, :id_tipo_ref, :id_referencia, :fecha_creacion)
        ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            ':id_usuario'    => $data['id_usuario'] ?? null,
            ':tipo'          => $data['tipo'] ?? 'info',
            ':asunto'        => $data['asunto'] ?? '',
            ':mensaje'       => $data['mensaje'] ?? '',
            ':canal'         => $data['canal'] ?? 'web',
            ':leida'         => $data['leida'] ?? 0,
            ':id_tipo_ref'   => $data['id_tipo_ref'] ?? null,
            ':id_referencia' => $data['id_referencia'] ?? null,
            ':fecha_creacion' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Obtiene una notificación por ID
     */
    public function getById(int $id): ?array
    {
        $query = "SELECT * FROM notificaciones WHERE id_notificacion = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Elimina una notificación
     */
    public function eliminar(int $id): bool
    {
        $query = "DELETE FROM notificaciones WHERE id_notificacion = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtiene últimas N notificaciones no leídas del usuario
     */
    public function getUltimas(int $id_usuario, int $limit = 5, string $canal = 'web'): array
    {
        $query = "
            SELECT 
                id_notificacion,
                tipo,
                asunto,
                mensaje,
                canal,
                fecha_creacion
            FROM notificaciones
            WHERE id_usuario = :id_usuario 
              AND leida = 0
              AND FIND_IN_SET(:canal, canal) > 0
            ORDER BY fecha_creacion DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':canal', $canal, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
