<?php
/**
 * models/VoluntarioModel.php
 * Modelo para gestionar voluntarios y asistencia.
 */

class VoluntarioModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Obtiene todos los voluntarios activos
     */
    public function getVoluntariosActivos(): array
    {
        $query = "
            SELECT 
                v.id_voluntario,
                v.id_usuario,
                v.telefono_contacto,
                v.zona_asignada,
                v.disponibilidad,
                v.activo,
                v.fecha_ingreso,
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.email
            FROM voluntarios v
            LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
            WHERE v.activo = 1
            ORDER BY u.nombre, u.apellido
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene actividades activas (planeada o en curso)
     */
    public function getActividadesActivas(): array
    {
        $query = "
            SELECT 
                id_actividad,
                titulo,
                descripcion,
                fecha_inicio,
                fecha_fin,
                zona,
                estado
            FROM actividades
            WHERE estado IN ('planeada', 'en_curso')
            ORDER BY fecha_inicio DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Registra asistencia de un voluntario a una actividad
     */
    public function registrarAsistencia(array $data): bool
    {
        $query = "
            INSERT INTO asistencia_voluntarios 
            (id_voluntario, id_actividad, presente, metodo, token_qr, fecha_registro)
            VALUES (:id_voluntario, :id_actividad, :presente, :metodo, :token_qr, :fecha_registro)
        ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            ':id_voluntario'  => $data['id_voluntario'] ?? null,
            ':id_actividad'   => $data['id_actividad'] ?? null,
            ':presente'       => $data['presente'] ?? 1,
            ':metodo'         => $data['metodo'] ?? 'manual',
            ':token_qr'       => $data['token_qr'] ?? null,
            ':fecha_registro' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Genera un token QR único para una actividad
     */
    public function generarTokenQR(int $id_voluntario, int $id_actividad): ?string
    {
        $token = bin2hex(random_bytes(32));

        $query = "
            INSERT INTO asistencia_voluntarios 
            (id_voluntario, id_actividad, presente, metodo, token_qr, fecha_registro)
            VALUES (:id_voluntario, :id_actividad, :presente, :metodo, :token_qr, :fecha_registro)
        ";

        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            ':id_voluntario'  => $id_voluntario,
            ':id_actividad'   => $id_actividad,
            ':presente'       => 0,
            ':metodo'         => 'qr',
            ':token_qr'       => $token,
            ':fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        return $result ? $token : null;
    }

    /**
     * Obtiene asistencia de una actividad
     */
    public function getAsistenciaPorActividad(int $id_actividad): array
    {
        $query = "
            SELECT 
                a.id_asistencia,
                a.id_voluntario,
                a.id_actividad,
                a.presente,
                a.metodo,
                a.token_qr,
                a.fecha_registro,
                v.id_usuario,
                u.nombre,
                u.apellido,
                u.email,
                v.zona_asignada
            FROM asistencia_voluntarios a
            LEFT JOIN voluntarios v ON a.id_voluntario = v.id_voluntario
            LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
            WHERE a.id_actividad = :id_actividad
            ORDER BY u.nombre, u.apellido
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_actividad' => $id_actividad]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Valida un QR y marca al voluntario como presente
     */
    public function validarQR(string $token_qr): bool
    {
        $query = "UPDATE asistencia_voluntarios SET presente = 1 WHERE token_qr = :token";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':token' => $token_qr]);
    }

    /**
     * Obtiene las asignaciones de un voluntario
     */
    public function getAsignacionesVoluntario(int $id_voluntario): array
    {
        $query = "
            SELECT 
                asn.id_asignacion,
                asn.id_voluntario,
                asn.id_entrega,
                asn.fecha_asignacion,
                asn.fecha_compromiso,
                e.id_entrega,
                e.cantidad_entregada,
                e.fecha_entrega,
                e.estado,
                b.id_beneficiario,
                bf.nombre AS benef_nombre,
                bf.apellido AS benef_apellido,
                bm.razon_social AS benef_razon_social
            FROM asignaciones_voluntario asn
            LEFT JOIN entregas e ON asn.id_entrega = e.id_entrega
            LEFT JOIN beneficiarios b ON e.id_beneficiario = b.id_beneficiario
            LEFT JOIN beneficiarios_fisicos bf ON b.id_beneficiario = bf.id_beneficiario
            LEFT JOIN beneficiarios_morales bm ON b.id_beneficiario = bm.id_beneficiario
            WHERE asn.id_voluntario = :id_voluntario
            ORDER BY asn.fecha_asignacion DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_voluntario' => $id_voluntario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
