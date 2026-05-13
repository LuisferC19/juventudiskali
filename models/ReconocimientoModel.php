<?php
/**
 * models/ReconocimientoModel.php
 * Modelo para gestionar reconocimientos en el sistema.
 */

class ReconocimientoModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Obtiene todos los reconocimientos, opcionalmente filtrados por tipo
     */
    public function getReconocimientos(?string $tipo = null): array
    {
        $query = "
            SELECT 
                r.id_reconocimiento,
                r.tipo,
                r.descripcion,
                r.archivo_pdf_url,
                r.fecha_emision,
                r.id_usuario_emisor,
                r.id_donador,
                r.id_campana,
                u_emisor.nombre AS emisor_nombre,
                u_emisor.apellido AS emisor_apellido,
                d.email AS donador_email,
                df.nombre AS donador_nombre,
                df.apellido AS donador_apellido,
                dm.razon_social AS donador_razon_social,
                c.nombre AS campana_nombre
            FROM reconocimientos r
            LEFT JOIN usuarios u_emisor ON r.id_usuario_emisor = u_emisor.id_usuario
            LEFT JOIN donadores d ON r.id_donador = d.id_donador
            LEFT JOIN donadores_fisicos df ON d.id_donador = df.id_donador
            LEFT JOIN donadores_morales dm ON d.id_donador = dm.id_donador
            LEFT JOIN campanas c ON r.id_campana = c.id_campana
        ";

        if (!empty($tipo)) {
            $query .= " WHERE r.tipo = :tipo";
        }

        $query .= " ORDER BY r.fecha_emision DESC";

        $stmt = $this->db->prepare($query);
        if (!empty($tipo)) {
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene KPIs de reconocimientos por tipo
     */
    public function getKPIsPorTipo(): array
    {
        $query = "
            SELECT 
                tipo,
                COUNT(*) as total
            FROM reconocimientos
            GROUP BY tipo
            ORDER BY total DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Emite un nuevo reconocimiento
     */
    public function emitir(array $data): bool
    {
        $query = "
            INSERT INTO reconocimientos 
            (id_donador, id_campana, tipo, descripcion, archivo_pdf_url, fecha_emision, id_usuario_emisor)
            VALUES (:id_donador, :id_campana, :tipo, :descripcion, :archivo_pdf_url, :fecha_emision, :id_usuario_emisor)
        ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            ':id_donador'         => $data['id_donador'] ?? null,
            ':id_campana'         => $data['id_campana'] ?? null,
            ':tipo'               => $data['tipo'] ?? 'otro',
            ':descripcion'        => $data['descripcion'] ?? null,
            ':archivo_pdf_url'    => $data['archivo_pdf_url'] ?? null,
            ':fecha_emision'      => date('Y-m-d H:i:s'),
            ':id_usuario_emisor'  => $data['id_usuario_emisor'] ?? null,
        ]);
    }

    /**
     * Obtiene un reconocimiento por ID
     */
    public function getById(int $id): ?array
    {
        $query = "
            SELECT 
                r.id_reconocimiento,
                r.tipo,
                r.descripcion,
                r.archivo_pdf_url,
                r.fecha_emision,
                r.id_usuario_emisor,
                r.id_donador,
                r.id_campana,
                u_emisor.nombre AS emisor_nombre,
                u_emisor.apellido AS emisor_apellido,
                d.email AS donador_email,
                df.nombre AS donador_nombre,
                df.apellido AS donador_apellido,
                dm.razon_social AS donador_razon_social,
                c.nombre AS campana_nombre
            FROM reconocimientos r
            LEFT JOIN usuarios u_emisor ON r.id_usuario_emisor = u_emisor.id_usuario
            LEFT JOIN donadores d ON r.id_donador = d.id_donador
            LEFT JOIN donadores_fisicos df ON d.id_donador = df.id_donador
            LEFT JOIN donadores_morales dm ON d.id_donador = dm.id_donador
            LEFT JOIN campanas c ON r.id_campana = c.id_campana
            WHERE r.id_reconocimiento = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Elimina un reconocimiento
     */
    public function eliminar(int $id): bool
    {
        $query = "DELETE FROM reconocimientos WHERE id_reconocimiento = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
