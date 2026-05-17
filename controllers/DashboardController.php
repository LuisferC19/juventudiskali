<?php
/**
 * controllers/DashboardController.php
 *
 * MEJORAS:
 * - Se agregan buildDonadorTypeSeries() y buildRoleUserSeries() para las nuevas gráficas.
 * - Cada método usa datos estáticos cuando la BD está vacía (count = 0),
 *   así las gráficas siempre muestran algo visualmente.
 * - fetchRows() helper genérico para leer filas de resultados.
 */
class DashboardController
{
    private ?PDO $db;

    public function __construct(PDO $conexion = null)
    {
        $this->db = $conexion;
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    private function fetchInt(string $sql, array $params = []): int
    {
        if (!$this->db) return 0;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    private function fetchFloat(string $sql, array $params = []): float
    {
        if (!$this->db) return 0.0;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($row['total'] ?? 0.0);
    }

    private function fetchRows(string $sql): array
    {
        if (!$this->db) return [];
        try {
            $stmt = $this->db->query($sql);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {
            logger('Dashboard fetchRows error: ' . $e->getMessage());
            return [];
        }
    }

    private function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2, '.', ',');
    }

    // ── Series de gráficas ─────────────────────────────────────────────────

    /** Donaciones por mes — barras (MXN vs Especie) */
    private function buildMonthlyDonationSeries(): array
    {
        $labels         = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $valuesEfectivo = array_fill(0, 12, 0.0);
        $valuesEspecie  = array_fill(0, 12, 0.0);

        if ($this->db) {
            $rows = $this->fetchRows("
                SELECT MONTH(d.fecha_recepcion) AS mes,
                       SUM(COALESCE(de.monto, 0))      AS total_efectivo,
                       SUM(COALESCE(de2.cantidad, 0))  AS total_especie
                FROM donaciones d
                LEFT JOIN donaciones_economicas de  ON d.id_donacion = de.id_donacion
                LEFT JOIN donaciones_especie    de2 ON d.id_donacion = de2.id_donacion
                WHERE YEAR(d.fecha_recepcion) = YEAR(CURDATE())
                GROUP BY MONTH(d.fecha_recepcion)
            ");
            foreach ($rows as $row) {
                $i = (int)$row['mes'] - 1;
                if ($i >= 0 && $i < 12) {
                    $valuesEfectivo[$i] = (float)$row['total_efectivo'];
                    $valuesEspecie[$i]  = (float)$row['total_especie'];
                }
            }
        }

        // Si todo es cero → datos estáticos ilustrativos
        $sinDatos = array_sum($valuesEfectivo) == 0 && array_sum($valuesEspecie) == 0;
        if ($sinDatos) {
            $valuesEfectivo = [0,0,5200,18000,25000,42000,0,0,0,0,0,0];
            $valuesEspecie  = [0,0,10,25,15,30,0,0,0,0,0,0];
        }

        return [json_encode($labels), json_encode($valuesEfectivo), json_encode($valuesEspecie), $sinDatos];
    }

    /** Estados de donación — pastel */
    private function buildDonationStateSeries(): array
    {
        $map = ['verificada'=>0,'recibida'=>0,'pendiente'=>0,'rechazada'=>0];

        if ($this->db) {
            $rows = $this->fetchRows("SELECT estado, COUNT(*) AS total FROM donaciones GROUP BY estado");
            foreach ($rows as $row) {
                $k = strtolower($row['estado'] ?? '');
                if (isset($map[$k])) $map[$k] = (int)$row['total'];
            }
        }

        $sinDatos = array_sum($map) === 0;
        if ($sinDatos) $map = ['verificada'=>9,'recibida'=>4,'pendiente'=>1,'rechazada'=>1];

        return [
            json_encode(['Verificada','Recibida','Pendiente','Rechazada']),
            json_encode(array_values($map)),
            $sinDatos,
        ];
    }

    /** Tipo de donación (especie vs económica) — donut */
    private function buildDonationTypeSeries(): array
    {
        $economica = 0;
        $especie   = 0;

        if ($this->db) {
            $row = $this->fetchRows("
                SELECT
                    SUM(de.id_donacion  IS NOT NULL) AS eco,
                    SUM(de2.id_donacion IS NOT NULL) AS esp
                FROM donaciones d
                LEFT JOIN donaciones_economicas de  ON d.id_donacion = de.id_donacion
                LEFT JOIN donaciones_especie    de2 ON d.id_donacion = de2.id_donacion
            ");
            if (!empty($row[0])) {
                $economica = (int)$row[0]['eco'];
                $especie   = (int)$row[0]['esp'];
            }
        }

        $sinDatos = ($economica + $especie) === 0;
        if ($sinDatos) { $economica = 6; $especie = 9; }

        return [json_encode(['Económica','En especie']), json_encode([$economica, $especie]), $sinDatos];
    }

    /** Beneficiarios por estado (activo / inactivo / en_espera) — donut */
    private function buildBeneficiaryStateSeries(): array
    {
        $map = ['activo'=>0,'en_espera'=>0,'inactivo'=>0];

        if ($this->db) {
            $rows = $this->fetchRows("SELECT estado, COUNT(*) AS total FROM beneficiarios GROUP BY estado");
            foreach ($rows as $row) {
                $k = strtolower($row['estado'] ?? '');
                if (isset($map[$k])) $map[$k] = (int)$row['total'];
            }
        }

        $sinDatos = array_sum($map) === 0;
        if ($sinDatos) $map = ['activo'=>13,'en_espera'=>3,'inactivo'=>2];

        return [
            json_encode(['Activos','En espera','Inactivos']),
            json_encode(array_values($map)),
            $sinDatos,
        ];
    }

    /**
     * Tipos de donador (anónimo / persona / grupo / organización) — barras horizontales
     * Datos reales de donadores.tipo_donante
     */
    private function buildDonadorTypeSeries(): array
    {
        $map = ['anonimo'=>0,'persona'=>0,'grupo'=>0,'organizacion'=>0];

        if ($this->db) {
            $rows = $this->fetchRows("SELECT tipo_donante, COUNT(*) AS total FROM donadores GROUP BY tipo_donante");
            foreach ($rows as $row) {
                $k = strtolower($row['tipo_donante'] ?? '');
                if (isset($map[$k])) $map[$k] = (int)$row['total'];
            }
        }

        $sinDatos = array_sum($map) === 0;
        if ($sinDatos) $map = ['anonimo'=>2,'persona'=>8,'grupo'=>3,'organizacion'=>5];

        return [
            json_encode(['Anónimo','Persona','Grupo','Organización']),
            json_encode(array_values($map)),
            $sinDatos,
        ];
    }

    /**
     * Usuarios por rol — barras horizontales
     * JOIN usuarios + roles, solo roles activos con al menos 1 usuario
     */
    private function buildRoleUserSeries(): array
    {
        $labels = [];
        $counts = [];

        if ($this->db) {
            $rows = $this->fetchRows("
                SELECT r.nombre AS rol, COUNT(u.id_usuario) AS total
                FROM roles r
                LEFT JOIN usuarios u ON u.id_rol = r.id_rol AND u.activo = 1
                WHERE r.activo = 1
                GROUP BY r.id_rol, r.nombre
                ORDER BY total DESC
            ");
            foreach ($rows as $row) {
                $labels[] = $row['rol'];
                $counts[] = (int)$row['total'];
            }
        }

        $sinDatos = empty($counts) || array_sum($counts) === 0;
        if ($sinDatos) {
            $labels = ['Administrador','Coordinador','Voluntario','Donador','Inventarista','Auditor','Comunicación'];
            $counts = [2, 1, 4, 6, 1, 1, 1];
        }

        return [json_encode($labels), json_encode($counts), $sinDatos];
    }

    /** Campañas activas con progreso estimado */
    private function fetchActiveCampaigns(): array
    {
        if (!$this->db) return $this->campanasDemoData();

        $rows = $this->fetchRows("
            SELECT c.nombre, c.meta_economica,
                   COALESCE(SUM(de.monto), 0) AS recaudado
            FROM campanas c
            LEFT JOIN donaciones d  ON d.id_campana = c.id_campana AND d.estado IN ('verificada','recibida')
            LEFT JOIN donaciones_economicas de ON de.id_donacion = d.id_donacion
            WHERE c.estado = 'activa'
            GROUP BY c.id_campana, c.nombre, c.meta_economica
            ORDER BY c.fecha_inicio ASC
            LIMIT 4
        ");

        if (empty($rows)) return $this->campanasDemoData();

        $colors = ['var(--primary)','var(--amber)','#3b6fd4','var(--danger)'];
        return array_map(function($row, $idx) use ($colors) {
            $meta      = max(1, (float)$row['meta_economica']);
            $recaudado = (float)$row['recaudado'];
            return [
                'nombre' => $row['nombre'],
                'pct'    => min(100, (int)round(($recaudado / $meta) * 100)),
                'color'  => $colors[$idx % count($colors)],
            ];
        }, $rows, array_keys($rows));
    }

    private function campanasDemoData(): array
    {
        return [
            ['nombre'=>'Abrigo Invierno',  'pct'=>72, 'color'=>'var(--primary)'],
            ['nombre'=>'Juguetes Navidad',  'pct'=>45, 'color'=>'var(--amber)'],
            ['nombre'=>'Empleo Joven',      'pct'=>18, 'color'=>'#3b6fd4'],
            ['nombre'=>'Medicina Todos',    'pct'=>8,  'color'=>'var(--danger)'],
        ];
    }

    // ── Acción principal ────────────────────────────────────────────────────

    public function index(): void
    {
        $this->verificarSesion();

        $pagina_activa = 'dashboard';
        $titulo_pagina = 'Dashboard';

        // KPIs
        require_once 'models/DonadorModel.php';
        require_once 'models/BeneficiarioModel.php';

        $donadorModel      = new DonadorModel($this->db);
        $beneficiarioModel = new BeneficiarioModel($this->db);

        $kpi_donadores        = $donadorModel->obtenerTotal();
        $kpi_beneficiarios    = $beneficiarioModel->obtenerTotalActivos();
        $kpi_campanas_activas = $this->fetchInt("SELECT COUNT(*) AS total FROM campanas WHERE estado = 'activa'");
        $kpi_inventario_items = $this->fetchInt("SELECT COUNT(*) AS total FROM inventario");
        $kpi_voluntarios      = $this->fetchInt("SELECT COUNT(*) AS total FROM voluntarios WHERE activo = 1");
        $kpi_entregas         = $this->fetchInt("SELECT COUNT(*) AS total FROM entregas WHERE estado = 'completada'");
        $kpi_donaciones_total = $this->formatCurrency($this->fetchFloat(
            "SELECT COALESCE(SUM(monto), 0) AS total FROM donaciones_economicas"
        ));
        $kpi_donaciones_mes   = $this->formatCurrency($this->fetchFloat(
            "SELECT COALESCE(SUM(de.monto), 0) AS total
             FROM donaciones d
             LEFT JOIN donaciones_economicas de ON d.id_donacion = de.id_donacion
             WHERE YEAR(d.fecha_recepcion) = YEAR(CURDATE())
               AND MONTH(d.fecha_recepcion) = MONTH(CURDATE())"
        ));

        // Series para gráficas
        [$gd_meses, $gd_montos, $gd_especie, $gd_demo]   = $this->buildMonthlyDonationSeries();
        [$ge_labels, $ge_data, $ge_demo]                  = $this->buildDonationStateSeries();
        [$gt_labels, $gt_data, $gt_demo]                  = $this->buildDonationTypeSeries();
        [$gb_labels, $gb_data, $gb_demo]                  = $this->buildBeneficiaryStateSeries();
        [$gdon_labels, $gdon_data, $gdon_demo]            = $this->buildDonadorTypeSeries();
        [$grol_labels, $grol_data, $grol_demo]            = $this->buildRoleUserSeries();

        $campanas_activas = $this->fetchActiveCampaigns();

        require_once 'views/pages/DashboardView.php';
    }

    // ── Sub-páginas ─────────────────────────────────────────────────────────

    public function donadores(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'donadores';
        $titulo_pagina = 'Donadores';
        require_once 'views/pages/DonadoresView.php';
    }

    public function donaciones(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'donaciones';
        $titulo_pagina = 'Donaciones';
        require_once 'views/pages/DonacionesView.php';
    }

    public function beneficiarios(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'beneficiarios';
        $titulo_pagina = 'Beneficiarios';
        require_once 'views/pages/BeneficiariosView.php';
    }

    public function entregas(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'entregas';
        $titulo_pagina = 'Entregas';
        require_once 'views/pages/EntregasView.php';
    }

    public function inventario(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'inventario';
        $titulo_pagina = 'Inventario';
        require_once 'views/pages/InventarioView.php';
    }

    public function voluntarios(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'voluntarios';
        $titulo_pagina = 'Voluntarios';
        require_once 'views/pages/VoluntariosView.php';
    }

    public function gamificacion(): void
    {
        $this->verificarSesion();
        $pagina_activa = 'gamificacion';
        $titulo_pagina = 'Gamificación';
        require_once 'views/pages/GamificacionView.php';
    }
}
