<?php
/**
 * controllers/ReportesController.php
 * Genera reportes PDF usando FPDF.
 */
class ReportesController
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    // ─────────────────────────────────────────────
    //  INDEX — vista con botones de reportes
    // ─────────────────────────────────────────────

    public function index(): void
    {
        $this->verificarSesion();

        $pagina_activa = 'reportes';
        $titulo_pagina = 'Reportes';

        require_once 'views/pages/ReportesView.php';
    }

    // ─────────────────────────────────────────────
    //  GENERAR — enruta al PDF correcto
    // ─────────────────────────────────────────────

    public function generar(): void
    {
        $this->verificarSesion();

        $tipo = $_GET['tipo'] ?? '';

        switch ($tipo) {
            case 'donadores':
                $this->pdfDonadores();
                break;
            case 'beneficiarios':
                $this->pdfBeneficiarios();
                break;
            case 'campanas':
                $this->pdfCampanas();
                break;
            default:
                header('Location: ' . BASE_URL . '/index.php?pagina=reportes&error=tipo_invalido');
                exit;
        }
    }

    // ─────────────────────────────────────────────
    //  Helper — instancia FPDF con header/footer
    // ─────────────────────────────────────────────

    private function crearPDF(string $titulo): object
    {
        require_once 'lib/fpdf/fpdf.php';

        // Clase anónima que extiende FPDF para personalizar cabecera y pie
        $tituloReporte = $titulo;

        $pdf = new class($tituloReporte) extends FPDF {
            private string $titulo;

            public function __construct(string $titulo)
            {
                parent::__construct('L', 'mm', 'A4'); // Landscape A4
                $this->titulo = $titulo;
            }

            public function Header(): void
            {
                // Franja de color superior
                $this->SetFillColor(10, 175, 160);
                $this->Rect(0, 0, 300, 18, 'F');

                // Nombre del sistema
                $this->SetFont('Arial', 'B', 11);
                $this->SetTextColor(255, 255, 255);
                $this->SetXY(10, 4);
                $this->Cell(140, 10, 'SISTEMA ISKALLI', 0, 0, 'L');

                // Fecha
                $this->SetFont('Arial', 'I', 9);
                $this->SetXY(150, 4);
                $this->Cell(137, 10, 'Generado: ' . date('d/m/Y H:i:s'), 0, 0, 'R');

                // Título del reporte
                $this->SetFont('Arial', 'B', 14);
                $this->SetTextColor(26, 46, 44);
                $this->SetXY(10, 22);
                $this->Cell(277, 10, utf8_decode($this->titulo), 0, 1, 'C');

                $this->Ln(4);
            }

            public function Footer(): void
            {
                $this->SetY(-12);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(150, 150, 150);
                $this->Cell(0, 10, 'Página ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
            }
        };

        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 18);

        return $pdf;
    }

    // ─────────────────────────────────────────────
    //  PDF: Donadores
    // ─────────────────────────────────────────────

    private function pdfDonadores(): void
    {
        $stmt = $this->db->query(
            "SELECT id, nombre, correo, telefono, tipo_donador, fecha_registro
             FROM donadores
             ORDER BY fecha_registro DESC"
        );
        $filas = $stmt->fetchAll();

        $pdf = $this->crearPDF('REPORTE DE DONADORES');

        // Cabecera de tabla
        $pdf->SetFillColor(10, 175, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(15,  9, '#',               1, 0, 'C', true);
        $pdf->Cell(65,  9, 'Nombre',          1, 0, 'L', true);
        $pdf->Cell(70,  9, 'Correo',          1, 0, 'L', true);
        $pdf->Cell(35,  9, 'Teléfono',        1, 0, 'C', true);
        $pdf->Cell(42,  9, 'Tipo',            1, 0, 'C', true);
        $pdf->Cell(40,  9, 'Fecha registro',  1, 1, 'C', true);

        // Cuerpo
        $pdf->SetTextColor(26, 46, 44);
        $pdf->SetFont('Arial', '', 9);
        $fill = false;

        foreach ($filas as $f) {
            $pdf->SetFillColor(240, 253, 251);
            $pdf->Cell(15,  8, $f['id'],                                    1, 0, 'C', $fill);
            $pdf->Cell(65,  8, utf8_decode($f['nombre'] ?? ''),             1, 0, 'L', $fill);
            $pdf->Cell(70,  8, utf8_decode($f['correo'] ?? ''),             1, 0, 'L', $fill);
            $pdf->Cell(35,  8, utf8_decode($f['telefono'] ?? ''),           1, 0, 'C', $fill);
            $pdf->Cell(42,  8, utf8_decode($f['tipo_donador'] ?? ''),       1, 0, 'C', $fill);
            $pdf->Cell(40,  8, $f['fecha_registro'] ?? '',                  1, 1, 'C', $fill);
            $fill = !$fill;
        }

        // Total
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(10, 175, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(267, 9, 'Total de donadores registrados: ' . count($filas), 1, 1, 'R', true);

        $pdf->Output('I', 'Reporte_Donadores_' . date('Ymd') . '.pdf');
        exit;
    }

    // ─────────────────────────────────────────────
    //  PDF: Beneficiarios
    // ─────────────────────────────────────────────

    private function pdfBeneficiarios(): void
    {
        $stmt = $this->db->query(
            "SELECT id, nombre, apellidos, edad, municipio, estatus, fecha_registro
             FROM beneficiarios
             ORDER BY fecha_registro DESC"
        );
        $filas = $stmt->fetchAll();

        $pdf = $this->crearPDF('REPORTE DE BENEFICIARIOS');

        // Cabecera
        $pdf->SetFillColor(10, 175, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(15,  9, '#',              1, 0, 'C', true);
        $pdf->Cell(60,  9, 'Nombre',         1, 0, 'L', true);
        $pdf->Cell(60,  9, 'Apellidos',      1, 0, 'L', true);
        $pdf->Cell(18,  9, 'Edad',           1, 0, 'C', true);
        $pdf->Cell(52,  9, 'Municipio',      1, 0, 'L', true);
        $pdf->Cell(32,  9, 'Estatus',        1, 0, 'C', true);
        $pdf->Cell(40,  9, 'Fecha reg.',     1, 1, 'C', true);

        // Cuerpo
        $pdf->SetTextColor(26, 46, 44);
        $pdf->SetFont('Arial', '', 9);
        $fill = false;

        foreach ($filas as $f) {
            $pdf->SetFillColor(240, 253, 251);
            $pdf->Cell(15,  8, $f['id'],                                   1, 0, 'C', $fill);
            $pdf->Cell(60,  8, utf8_decode($f['nombre'] ?? ''),            1, 0, 'L', $fill);
            $pdf->Cell(60,  8, utf8_decode($f['apellidos'] ?? ''),         1, 0, 'L', $fill);
            $pdf->Cell(18,  8, $f['edad'] ?? '',                           1, 0, 'C', $fill);
            $pdf->Cell(52,  8, utf8_decode($f['municipio'] ?? ''),         1, 0, 'L', $fill);
            $pdf->Cell(32,  8, utf8_decode($f['estatus'] ?? ''),           1, 0, 'C', $fill);
            $pdf->Cell(40,  8, $f['fecha_registro'] ?? '',                 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(10, 175, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(277, 9, 'Total de beneficiarios: ' . count($filas), 1, 1, 'R', true);

        $pdf->Output('I', 'Reporte_Beneficiarios_' . date('Ymd') . '.pdf');
        exit;
    }

    // ─────────────────────────────────────────────
    //  PDF: Campañas
    // ─────────────────────────────────────────────

    private function pdfCampanas(): void
    {
        $stmt = $this->db->query(
            "SELECT id, nombre, descripcion, fecha_inicio, fecha_fin, estatus, meta_monto
             FROM campanas
             ORDER BY fecha_inicio DESC"
        );
        $filas = $stmt->fetchAll();

        $pdf = $this->crearPDF('REPORTE DE CAMPAÑAS');

        // Cabecera
        $pdf->SetFillColor(10, 175, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(12,  9, '#',            1, 0, 'C', true);
        $pdf->Cell(75,  9, 'Nombre',       1, 0, 'L', true);
        $pdf->Cell(80,  9, 'Descripción',  1, 0, 'L', true);
        $pdf->Cell(30,  9, 'Inicio',       1, 0, 'C', true);
        $pdf->Cell(30,  9, 'Fin',          1, 0, 'C', true);
        $pdf->Cell(28,  9, 'Estatus',      1, 0, 'C', true);
        $pdf->Cell(32,  9, 'Meta ($)',     1, 1, 'R', true);

        // Cuerpo
        $pdf->SetTextColor(26, 46, 44);
        $pdf->SetFont('Arial', '', 9);
        $fill = false;

        foreach ($filas as $f) {
            $pdf->SetFillColor(240, 253, 251);
            $pdf->Cell(12,  8, $f['id'],                                           1, 0, 'C', $fill);
            $pdf->Cell(75,  8, utf8_decode(mb_strimwidth($f['nombre'] ?? '', 0, 40, '…')),      1, 0, 'L', $fill);
            $pdf->Cell(80,  8, utf8_decode(mb_strimwidth($f['descripcion'] ?? '', 0, 45, '…')), 1, 0, 'L', $fill);
            $pdf->Cell(30,  8, $f['fecha_inicio'] ?? '',                            1, 0, 'C', $fill);
            $pdf->Cell(30,  8, $f['fecha_fin'] ?? '',                               1, 0, 'C', $fill);
            $pdf->Cell(28,  8, utf8_decode($f['estatus'] ?? ''),                    1, 0, 'C', $fill);
            $pdf->Cell(32,  8, '$' . number_format((float)($f['meta_monto'] ?? 0), 2), 1, 1, 'R', $fill);
            $fill = !$fill;
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(10, 175, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(287, 9, 'Total de campañas: ' . count($filas), 1, 1, 'R', true);

        $pdf->Output('I', 'Reporte_Campanas_' . date('Ymd') . '.pdf');
        exit;
    }
}
