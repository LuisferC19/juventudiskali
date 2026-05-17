<?php
/**
 * models/ReporteModel.php
 * Modelo para el módulo de Reportes.
 * Centraliza todas las consultas que alimentan los reportes del sistema.
 */

class ReporteModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  DONADORES
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todos los donadores con nombre resuelto según su tipo,
     * correo, teléfono, tipo de donante y estatus activo/inactivo.
     */
    public function getDonadores(): array
    {
        $sql = "
            SELECT
                d.id_donador,
                CASE
                    WHEN d.tipo_donante = 'anonimo' THEN 'Anónimo'
                    WHEN dg.id_donador  IS NOT NULL  THEN dg.nombre_grupo
                    WHEN df.id_donador  IS NOT NULL  THEN CONCAT(df.nombre, ' ', df.apellido)
                    WHEN dm.id_donador  IS NOT NULL  THEN dm.razon_social
                    ELSE 'Sin datos'
                END AS nombre_completo,
                COALESCE(d.email,    '—') AS email,
                COALESCE(d.telefono, '—') AS telefono,
                CASE d.tipo_donante
                    WHEN 'persona'      THEN 'Persona física'
                    WHEN 'organizacion' THEN 'Organización'
                    WHEN 'grupo'        THEN 'Grupo'
                    WHEN 'anonimo'      THEN 'Anónimo'
                    ELSE d.tipo_donante
                END AS tipo_label,
                d.activo,
                d.created_at
            FROM donadores d
            LEFT JOIN donadores_fisicos  df ON d.id_donador = df.id_donador
            LEFT JOIN donadores_morales  dm ON d.id_donador = dm.id_donador
            LEFT JOIN donadores_grupos   dg ON d.id_donador = dg.id_donador
            ORDER BY nombre_completo ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen estadístico de donadores (total, activos, inactivos).
     */
    public function getResumenDonadores(): array
    {
        $filas     = $this->getDonadores();
        $total     = count($filas);
        $activos   = count(array_filter($filas, fn($f) => (bool)$f['activo']));
        $inactivos = $total - $activos;

        return compact('total', 'activos', 'inactivos', 'filas');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  BENEFICIARIOS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todos los beneficiarios con nombre, apellidos, edad,
     * municipio de su comunidad y estatus de atención.
     */
    public function getBeneficiarios(): array
    {
        $sql = "
            SELECT
                b.id_beneficiario,
                COALESCE(bf.nombre,   bm.razon_social, '—') AS nombre,
                COALESCE(bf.apellido, '—')                   AS apellidos,
                bf.edad,
                COALESCE(com.municipio, '—')                 AS municipio,
                CASE b.estado
                    WHEN 'activo'    THEN 'Activo'
                    WHEN 'inactivo'  THEN 'Inactivo'
                    WHEN 'en_espera' THEN 'En espera'
                    ELSE b.estado
                END AS estatus,
                b.tipo_persona
            FROM beneficiarios b
            LEFT JOIN beneficiarios_fisicos  bf  ON b.id_beneficiario = bf.id_beneficiario
            LEFT JOIN beneficiarios_morales  bm  ON b.id_beneficiario = bm.id_beneficiario
            LEFT JOIN comunidades            com ON b.id_comunidad    = com.id_comunidad
            ORDER BY bf.apellido ASC, bf.nombre ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen estadístico de beneficiarios (total, activos, en espera, inactivos).
     */
    public function getResumenBeneficiarios(): array
    {
        $filas     = $this->getBeneficiarios();
        $total     = count($filas);
        $activos   = count(array_filter($filas, fn($f) => $f['estatus'] === 'Activo'));
        $espera    = count(array_filter($filas, fn($f) => $f['estatus'] === 'En espera'));
        $inactivos = count(array_filter($filas, fn($f) => $f['estatus'] === 'Inactivo'));

        return compact('total', 'activos', 'espera', 'inactivos', 'filas');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  CAMPAÑAS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todas las campañas con nombre, descripción, fechas,
     * estatus y meta económica.
     */
    public function getCampanas(): array
    {
        $sql = "
            SELECT
                id_campana,
                nombre,
                COALESCE(descripcion,   '—') AS descripcion,
                fecha_inicio,
                COALESCE(fecha_cierre,  '—') AS fecha_fin,
                CASE estado
                    WHEN 'activa'     THEN 'Activa'
                    WHEN 'finalizada' THEN 'Finalizada'
                    WHEN 'cancelada'  THEN 'Cancelada'
                    ELSE estado
                END                          AS estatus,
                COALESCE(meta_economica, 0)  AS meta_monto
            FROM campanas
            ORDER BY fecha_inicio DESC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen estadístico de campañas (total y meta económica acumulada).
     */
    public function getResumenCampanas(): array
    {
        $filas     = $this->getCampanas();
        $total     = count($filas);
        $totalMeta = array_sum(array_column($filas, 'meta_monto'));

        return compact('total', 'totalMeta', 'filas');
    }
}