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
     *
     * RF005: acepta un rango de fechas opcional (sobre d.created_at) para
     * que el reporte pueda filtrarse por periodo. $desde/$hasta van en
     * formato 'Y-m-d'; si vienen vacíos no se aplica filtro.
     */
    public function getDonadores(?string $desde = null, ?string $hasta = null): array
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
        ";

        [$whereSql, $params] = $this->construirFiltroFechas('d.created_at', $desde, $hasta);
        $sql .= $whereSql . " ORDER BY nombre_completo ASC";

        if (empty($params)) {
            // Sin filtro: consulta estática, sin parámetros de usuario.
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen estadístico de donadores (total, activos, inactivos).
     */
    public function getResumenDonadores(?string $desde = null, ?string $hasta = null): array
    {
        $filas     = $this->getDonadores($desde, $hasta);
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
     *
     * RF005: rango de fechas opcional sobre b.created_at (fecha de registro).
     */
    public function getBeneficiarios(?string $desde = null, ?string $hasta = null): array
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
        ";

        [$whereSql, $params] = $this->construirFiltroFechas('b.created_at', $desde, $hasta);
        $sql .= $whereSql . " ORDER BY bf.apellido ASC, bf.nombre ASC";

        if (empty($params)) {
            // Sin filtro: consulta estática, sin parámetros de usuario.
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen estadístico de beneficiarios (total, activos, en espera, inactivos).
     */
    public function getResumenBeneficiarios(?string $desde = null, ?string $hasta = null): array
    {
        $filas     = $this->getBeneficiarios($desde, $hasta);
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
     *
     * RF005: rango de fechas opcional sobre fecha_inicio de la campaña.
     */
    public function getCampanas(?string $desde = null, ?string $hasta = null): array
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
        ";

        [$whereSql, $params] = $this->construirFiltroFechas('fecha_inicio', $desde, $hasta);
        $sql .= $whereSql . " ORDER BY fecha_inicio DESC";

        if (empty($params)) {
            // Sin filtro: consulta estática, sin parámetros de usuario.
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen estadístico de campañas (total y meta económica acumulada).
     */
    public function getResumenCampanas(?string $desde = null, ?string $hasta = null): array
    {
        $filas     = $this->getCampanas($desde, $hasta);
        $total     = count($filas);
        $totalMeta = array_sum(array_column($filas, 'meta_monto'));

        return compact('total', 'totalMeta', 'filas');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  RF005 — Helper de filtro de fechas
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Construye la cláusula WHERE y el arreglo de parámetros para filtrar
     * una columna de fecha/fecha-hora por un rango [$desde, $hasta].
     * Valida que ambos vengan en formato Y-m-d antes de usarlos; cualquier
     * valor inválido o vacío se ignora sin lanzar error (filtro opcional).
     *
     * @return array{0: string, 1: array} [clausulaWhere, parametrosBind]
     */
    private function construirFiltroFechas(string $columna, ?string $desde, ?string $hasta): array
    {
        $condiciones = [];
        $params      = [];

        if ($desde && $this->esFechaValida($desde)) {
            $condiciones[] = "{$columna} >= ?";
            $params[]      = $desde . ' 00:00:00';
        }

        if ($hasta && $this->esFechaValida($hasta)) {
            $condiciones[] = "{$columna} <= ?";
            $params[]      = $hasta . ' 23:59:59';
        }

        if (empty($condiciones)) {
            return ['', []];
        }

        return [' WHERE ' . implode(' AND ', $condiciones), $params];
    }

    private function esFechaValida(string $fecha): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
}