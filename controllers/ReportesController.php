<?php
/**
 * controllers/ReportesController.php
 *
 * Genera reportes como páginas HTML imprimibles (sin librerías externas).
 * Se abren en una nueva pestaña; el usuario puede imprimir o guardar como PDF
 * usando la opción de impresión del navegador (Ctrl+P).
 *
 * CORRECCIÓN: El archivo original tenía conflictos de merge de Git sin resolver
 * (marcadores <<<<<<< HEAD / >>>>>>> ...) que rompían el archivo completo.
 * Se resolvió conservando la versión HEAD (renderizado HTML + ReporteModel),
 * que es la implementación correcta y funcional del sistema.
 *
 * Rutas disponibles:
 *   ?pagina=reportes                                    → vista con botones
 *   ?pagina=reportes&accion=generar&tipo=donadores      → reporte donadores
 *   ?pagina=reportes&accion=generar&tipo=beneficiarios  → reporte beneficiarios
 *   ?pagina=reportes&accion=generar&tipo=campanas       → reporte campañas
 */
class ReportesController
{
    private PDO          $db;
    private ReporteModel $model;

    public function __construct(PDO $conexion)
    {
        $this->db    = $conexion;
        require_once 'models/ReporteModel.php';
        $this->model = new ReporteModel($conexion);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Seguridad de sesión
    // ─────────────────────────────────────────────────────────────────────────

    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  INDEX — vista con tarjetas de reportes
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $this->verificarSesion();

        $pagina_activa = 'reportes';
        $titulo_pagina = 'Reportes';

        require_once 'views/pages/ReportesView.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  GENERAR — enruta al reporte solicitado
    // ─────────────────────────────────────────────────────────────────────────

    public function generar(): void
    {
        $this->verificarSesion();

        $tipo = trim($_GET['tipo'] ?? '');

        switch ($tipo) {
            case 'donadores':
                $this->reporteDonadores();
                break;
            case 'beneficiarios':
                $this->reporteBeneficiarios();
                break;
            case 'campanas':
                $this->reporteCampanas();
                break;
            default:
                header('Location: ' . BASE_URL . '/index.php?pagina=reportes&error=tipo_invalido');
                exit;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helper — renderiza el HTML completo del reporte y termina
    // ─────────────────────────────────────────────────────────────────────────

    private function renderReporte(string $titulo, string $subtitulo, string $cuerpo): void
    {
        $fecha     = date('d/m/Y  H:i:s');
        $appNombre = defined('APP_NAME') ? APP_NAME : 'Juventud Iskali';

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>{$titulo} — {$appNombre}</title>
          <style>
            /* ── Variables ── */
            :root {
              --verde:    #0aafa0;
              --verde-dk: #087a70;
              --text:     #1a2e2c;
              --muted:    #5a8a84;
              --border:   #d0ecea;
              --fondo:    #f4fffe;
              --alt-row:  #e8f9f7;
            }

            /* ── Reset básico ── */
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            html { font-size: 14px; }
            body {
              font-family: 'Segoe UI', Arial, sans-serif;
              background: var(--fondo);
              color: var(--text);
              padding: 0;
            }

            /* ── Encabezado del reporte ── */
            .report-header {
              background: var(--verde);
              color: #fff;
              padding: 18px 32px 14px;
              display: flex;
              justify-content: space-between;
              align-items: center;
            }
            .report-header .org   { font-size: 12px; opacity: .85; }
            .report-header .fecha { font-size: 11px; opacity: .80; text-align: right; }

            .report-title-bar {
              background: #fff;
              padding: 16px 32px 12px;
              border-bottom: 2px solid var(--verde);
            }
            .report-title-bar h1 {
              font-size: 20px;
              font-weight: 700;
              color: var(--text);
              letter-spacing: -.3px;
            }
            .report-title-bar .subtitle {
              font-size: 12px;
              color: var(--muted);
              margin-top: 4px;
            }

            /* ── Contenedor principal ── */
            .report-body { padding: 24px 32px 40px; }

            /* ── Tabla ── */
            table {
              width: 100%;
              border-collapse: collapse;
              margin-top: 0;
              font-size: 12.5px;
            }
            thead tr {
              background: var(--verde);
              color: #fff;
            }
            thead th {
              padding: 9px 10px;
              text-align: left;
              font-weight: 600;
              font-size: 11.5px;
              white-space: nowrap;
            }
            thead th.c { text-align: center; }
            thead th.r { text-align: right; }

            tbody tr:nth-child(even) { background: var(--alt-row); }
            tbody tr:nth-child(odd)  { background: #fff; }
            tbody tr:hover           { background: #c8f0ed; }

            tbody td {
              padding: 8px 10px;
              border-bottom: 1px solid var(--border);
              color: var(--text);
            }
            tbody td.c { text-align: center; }
            tbody td.r { text-align: right; }

            /* ── Fila de totales ── */
            .totals-row {
              background: var(--verde) !important;
              color: #fff !important;
              font-weight: 700;
            }
            .totals-row td {
              color: #fff !important;
              border-bottom: none !important;
              padding: 9px 10px;
            }

            /* ── Badges de estado ── */
            .badge {
              display: inline-block;
              padding: 2px 10px;
              border-radius: 99px;
              font-size: 10.5px;
              font-weight: 600;
              white-space: nowrap;
            }
            .badge-activo     { background: #d1fae5; color: #065f46; }
            .badge-inactivo   { background: #fee2e2; color: #991b1b; }
            .badge-espera     { background: #fef3c7; color: #92400e; }
            .badge-activa     { background: #d1fae5; color: #065f46; }
            .badge-finalizada { background: #dbeafe; color: #1e40af; }
            .badge-cancelada  { background: #fee2e2; color: #991b1b; }

            /* ── Pie del reporte ── */
            .report-footer {
              background: var(--verde);
              color: #fff;
              text-align: center;
              padding: 9px;
              font-size: 11px;
              opacity: .9;
            }

            /* ── Botón imprimir (no se imprime) ── */
            .print-btn {
              position: fixed;
              bottom: 28px;
              right: 28px;
              background: var(--verde);
              color: #fff;
              border: none;
              border-radius: 50px;
              padding: 13px 26px;
              font-size: 14px;
              font-weight: 700;
              cursor: pointer;
              box-shadow: 0 4px 20px rgba(10,175,160,.4);
              display: flex;
              align-items: center;
              gap: 8px;
              transition: background .2s;
            }
            .print-btn:hover { background: var(--verde-dk); }

            /* ── Media print ── */
            @media print {
              body        { background: #fff; font-size: 10pt; }
              .print-btn  { display: none !important; }
              table       { page-break-inside: auto; font-size: 9pt; }
              tr          { page-break-inside: avoid; page-break-after: auto; }
              thead       { display: table-header-group; }
              tfoot       { display: table-footer-group; }
              .report-header,
              .report-title-bar,
              .report-footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
              thead tr    { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
              .totals-row { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
              tbody tr:nth-child(even) { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
              .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            }
          </style>
        </head>
        <body>

          <!-- Encabezado corporativo -->
          <div class="report-header">
            <div class="org">
              <strong style="font-size:14px;">{$appNombre}</strong><br>
              Sistema de Gestión Social
            </div>
            <div class="fecha">
              Generado el:<br>
              <strong>{$fecha}</strong>
            </div>
          </div>

          <!-- Título del reporte -->
          <div class="report-title-bar">
            <h1>{$titulo}</h1>
            <p class="subtitle">{$subtitulo}</p>
          </div>

          <!-- Contenido principal -->
          <div class="report-body">
            {$cuerpo}
          </div>

          <!-- Pie -->
          <div class="report-footer">
            Sistema de Gestión Juventud Iskali &nbsp;|&nbsp; {$appNombre}
          </div>

          <!-- Botón flotante de impresión -->
          <button class="print-btn" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                 stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M6 9V2h12v7"/><rect x="2" y="9" width="20" height="9" rx="2"/>
              <path d="M6 18v4h12v-4"/><circle cx="18" cy="13" r="1" fill="currentColor"/>
            </svg>
            Imprimir / Guardar PDF
          </button>

        </body>
        </html>
        HTML;

        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helper — badge de estado con color
    // ─────────────────────────────────────────────────────────────────────────

    private function badge(string $texto, string $clase): string
    {
        $claseSegura = htmlspecialchars($clase);
        $textoSeguro = htmlspecialchars($texto);
        return "<span class=\"badge badge-{$claseSegura}\">{$textoSeguro}</span>";
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REPORTE: Donadores
    // ─────────────────────────────────────────────────────────────────────────

    private function reporteDonadores(): void
    {
        $data      = $this->model->getResumenDonadores();
        $filas     = $data['filas'];
        $total     = $data['total'];
        $activos   = $data['activos'];
        $inactivos = $data['inactivos'];

        $filasHtml = '';
        foreach ($filas as $i => $f) {
            $n            = $i + 1;
            $nombre       = htmlspecialchars($f['nombre_completo'] ?? '—');
            $email        = htmlspecialchars($f['email']);
            $tel          = htmlspecialchars($f['telefono']);
            $tipo         = htmlspecialchars($f['tipo_label']);
            $estatusBadge = (bool)$f['activo']
                ? $this->badge('Activo',   'activo')
                : $this->badge('Inactivo', 'inactivo');

            $filasHtml .= "<tr>
              <td class=\"c\">{$n}</td>
              <td>{$nombre}</td>
              <td>{$email}</td>
              <td class=\"c\">{$tel}</td>
              <td class=\"c\">{$tipo}</td>
              <td class=\"c\">{$estatusBadge}</td>
            </tr>";
        }

        $filasHtml .= "<tr class=\"totals-row\">
          <td colspan=\"5\" style=\"text-align:right;\">
            Total donadores: {$total} &nbsp;|&nbsp; Activos: {$activos} &nbsp;|&nbsp; Inactivos: {$inactivos}
          </td>
          <td></td>
        </tr>";

        $cuerpo = "
        <table>
          <thead>
            <tr>
              <th class=\"c\" style=\"width:40px;\">#</th>
              <th>Nombre / Razón social</th>
              <th>Correo electrónico</th>
              <th class=\"c\" style=\"width:130px;\">Teléfono</th>
              <th class=\"c\" style=\"width:130px;\">Tipo</th>
              <th class=\"c\" style=\"width:100px;\">Estatus</th>
            </tr>
          </thead>
          <tbody>{$filasHtml}</tbody>
        </table>";

        $subtitulo = "Total: {$total} donadores   |   Activos: {$activos}   |   Inactivos: {$inactivos}";
        $this->renderReporte('LISTADO DE DONADORES', $subtitulo, $cuerpo);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REPORTE: Beneficiarios
    // ─────────────────────────────────────────────────────────────────────────

    private function reporteBeneficiarios(): void
    {
        $data      = $this->model->getResumenBeneficiarios();
        $filas     = $data['filas'];
        $total     = $data['total'];
        $activos   = $data['activos'];
        $espera    = $data['espera'];
        $inactivos = $data['inactivos'];

        $filasHtml = '';
        foreach ($filas as $i => $f) {
            $n         = $i + 1;
            $nombre    = htmlspecialchars($f['nombre']    ?? '—');
            $apellidos = htmlspecialchars($f['apellidos'] ?? '—');
            $edad      = ($f['edad'] !== null && $f['edad'] !== '')
                ? htmlspecialchars($f['edad']) . ' años'
                : '—';
            $municipio = htmlspecialchars($f['municipio'] ?? '—');
            $tipoLabel = $f['tipo_persona'] === 'moral' ? 'Moral' : 'Física';

            $badgeClase = match($f['estatus']) {
                'Activo'    => 'activo',
                'Inactivo'  => 'inactivo',
                'En espera' => 'espera',
                default     => 'inactivo',
            };
            $estatusBadge = $this->badge($f['estatus'], $badgeClase);

            $filasHtml .= "<tr>
              <td class=\"c\">{$n}</td>
              <td>{$nombre}</td>
              <td>{$apellidos}</td>
              <td class=\"c\">{$edad}</td>
              <td>{$municipio}</td>
              <td class=\"c\">{$estatusBadge}</td>
              <td class=\"c\">{$tipoLabel}</td>
            </tr>";
        }

        $filasHtml .= "<tr class=\"totals-row\">
          <td colspan=\"6\" style=\"text-align:right;\">
            Total: {$total} &nbsp;|&nbsp; Activos: {$activos} &nbsp;|&nbsp; En espera: {$espera} &nbsp;|&nbsp; Inactivos: {$inactivos}
          </td>
          <td></td>
        </tr>";

        $cuerpo = "
        <table>
          <thead>
            <tr>
              <th class=\"c\" style=\"width:40px;\">#</th>
              <th style=\"width:18%;\">Nombre</th>
              <th style=\"width:20%;\">Apellidos</th>
              <th class=\"c\" style=\"width:80px;\">Edad</th>
              <th>Municipio</th>
              <th class=\"c\" style=\"width:110px;\">Estatus</th>
              <th class=\"c\" style=\"width:80px;\">Tipo</th>
            </tr>
          </thead>
          <tbody>{$filasHtml}</tbody>
        </table>";

        $subtitulo = "Total: {$total}   |   Activos: {$activos}   |   En espera: {$espera}   |   Inactivos: {$inactivos}";
        $this->renderReporte('LISTADO DE BENEFICIARIOS', $subtitulo, $cuerpo);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REPORTE: Campañas
    // ─────────────────────────────────────────────────────────────────────────

    private function reporteCampanas(): void
    {
        $data      = $this->model->getResumenCampanas();
        $filas     = $data['filas'];
        $total     = $data['total'];
        $totalMeta = $data['totalMeta'];

        $filasHtml = '';
        foreach ($filas as $i => $f) {
            $n      = $i + 1;
            $nombre = htmlspecialchars($f['nombre']      ?? '—');
            $desc   = htmlspecialchars($f['descripcion'] ?? '—');
            $inicio = htmlspecialchars($f['fecha_inicio'] ?? '—');
            $fin    = htmlspecialchars($f['fecha_fin']    ?? '—');
            $meta   = '$' . number_format((float)$f['meta_monto'], 2);

            $badgeClase = match($f['estatus']) {
                'Activa'     => 'activa',
                'Finalizada' => 'finalizada',
                'Cancelada'  => 'cancelada',
                default      => 'inactivo',
            };
            $estatusBadge = $this->badge($f['estatus'], $badgeClase);

            $filasHtml .= "<tr>
              <td class=\"c\">{$n}</td>
              <td>{$nombre}</td>
              <td style=\"color:#5a8a84;font-size:11.5px;\">{$desc}</td>
              <td class=\"c\">{$inicio}</td>
              <td class=\"c\">{$fin}</td>
              <td class=\"c\">{$estatusBadge}</td>
              <td class=\"r\">{$meta}</td>
            </tr>";
        }

        $metaFormateada = '$' . number_format($totalMeta, 2);
        $filasHtml .= "<tr class=\"totals-row\">
          <td colspan=\"5\" style=\"text-align:right;\">
            Total campañas: {$total} &nbsp;|&nbsp; Meta acumulada:
          </td>
          <td></td>
          <td class=\"r\">{$metaFormateada}</td>
        </tr>";

        $cuerpo = "
        <table>
          <thead>
            <tr>
              <th class=\"c\" style=\"width:40px;\">#</th>
              <th style=\"width:22%;\">Nombre</th>
              <th>Descripción</th>
              <th class=\"c\" style=\"width:100px;\">Inicio</th>
              <th class=\"c\" style=\"width:100px;\">Cierre</th>
              <th class=\"c\" style=\"width:100px;\">Estatus</th>
              <th class=\"r\" style=\"width:100px;\">Meta ($)</th>
            </tr>
          </thead>
          <tbody>{$filasHtml}</tbody>
        </table>";

        $subtitulo = "Total: {$total} campañas   |   Meta económica acumulada: $" . number_format($totalMeta, 2);
        $this->renderReporte('LISTADO DE CAMPAÑAS', $subtitulo, $cuerpo);
    }
}