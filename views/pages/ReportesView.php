<?php
/**
 * views/pages/ReportesView.php
 * Vista principal del módulo de Reportes.
 * Muestra las tarjetas para generar cada reporte HTML imprimible.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content" style="padding:32px;background:var(--fondo,#f8fffe);min-height:calc(100vh - 72px);">

      <!-- ══ Encabezado ══ -->
      <div style="margin-bottom:28px;">
        <h2 style="font-family:'Syne',sans-serif;font-size:24px;font-weight:700;letter-spacing:-0.5px;color:var(--texto,#1a2e2c);margin-bottom:8px;">
          Reportes 📄
        </h2>
        <p style="font-size:14px;color:var(--muted,#5a8a84);max-width:620px;line-height:1.7;">
          Genera reportes en formato PDF de los módulos del sistema. Los archivos se abren directamente en tu navegador y puedes descargarlos o imprimirlos.
        </p>
      </div>

      <!-- ══ Tarjetas de reportes ══ -->
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:22px;">

        <!-- ── Donadores ── -->
        <div class="card" style="padding:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="width:52px;height:52px;background:rgba(10,175,160,0.12);border-radius:16px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#0aafa0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px;">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div>
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--texto,#1a2e2c);margin:0 0 6px;">
              Donadores
            </h3>
            <p style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;margin:0;">
              Listado completo de todos los donadores registrados con nombre, correo, teléfono y tipo.
            </p>
          </div>
          <a href="<?= BASE_URL ?>/index.php?pagina=reportes&accion=generar&tipo=donadores"
             target="_blank"
             style="background:var(--verde,#0aafa0);color:#fff;text-decoration:none;border-radius:12px;padding:11px 18px;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;align-self:flex-start;margin-top:auto;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;">
              <path d="M6 9V2h12v7"/><rect x="2" y="9" width="20" height="9" rx="2"/>
              <path d="M6 18v4h12v-4"/>
            </svg>
            Generar reporte
          </a>
        </div>

        <!-- ── Beneficiarios ── -->
        <div class="card" style="padding:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="width:52px;height:52px;background:rgba(33,150,243,0.10);border-radius:16px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2196f3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px;">
              <circle cx="9" cy="7" r="4"/>
              <path d="M3 21v-2a4 4 0 0 1 4-4h4"/>
              <circle cx="18" cy="11" r="3"/>
              <path d="M14 21v-1a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3v1"/>
            </svg>
          </div>
          <div>
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--texto,#1a2e2c);margin:0 0 6px;">
              Beneficiarios
            </h3>
            <p style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;margin:0;">
              Listado de beneficiarios con nombre, tipo, edad, municipio y estatus de atención.
            </p>
          </div>
          <a href="<?= BASE_URL ?>/index.php?pagina=reportes&accion=generar&tipo=beneficiarios"
             target="_blank"
             style="background:#2196f3;color:#fff;text-decoration:none;border-radius:12px;padding:11px 18px;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;align-self:flex-start;margin-top:auto;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;">
              <path d="M6 9V2h12v7"/><rect x="2" y="9" width="20" height="9" rx="2"/>
              <path d="M6 18v4h12v-4"/>
            </svg>
            Generar reporte
          </a>
        </div>

        <!-- ── Campañas ── -->
        <div class="card" style="padding:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="width:52px;height:52px;background:rgba(156,39,176,0.10);border-radius:16px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#9c27b0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px;">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
          </div>
          <div>
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--texto,#1a2e2c);margin:0 0 6px;">
              Campañas
            </h3>
            <p style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;margin:0;">
              Reporte de todas las campañas con nombre, fechas, estatus y meta económica acumulada.
            </p>
          </div>
          <a href="<?= BASE_URL ?>/index.php?pagina=reportes&accion=generar&tipo=campanas"
             target="_blank"
             style="background:#9c27b0;color:#fff;text-decoration:none;border-radius:12px;padding:11px 18px;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;align-self:flex-start;margin-top:auto;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;">
              <path d="M6 9V2h12v7"/><rect x="2" y="9" width="20" height="9" rx="2"/>
              <path d="M6 18v4h12v-4"/>
            </svg>
            Generar reporte
          </a>
        </div>

      </div><!-- /grid -->

      <!-- ══ Nota informativa ══ -->
      <div style="margin-top:28px;background:rgba(10,175,160,0.07);border:1px solid rgba(10,175,160,0.18);border-radius:16px;padding:18px 22px;font-size:13px;color:var(--muted,#5a8a84);line-height:1.7;">
        <strong style="color:var(--verde,#0aafa0);">💡 Tip:</strong>
        Los reportes se abren en una nueva pestaña del navegador. Desde ahí puedes imprimirlos o guardarlos como PDF usando la opción de impresión del navegador
        (<kbd style="background:#fff;border:1px solid var(--border,#d0ecea);border-radius:4px;padding:1px 5px;font-size:11px;">Ctrl+P</kbd>).
        Los datos corresponden a la información actual registrada en el sistema.
      </div>

      <?php if (!empty($_GET['error']) && $_GET['error'] === 'tipo_invalido'): ?>
        <div style="margin-top:16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:14px 20px;font-size:13px;color:#991b1b;">
          ⚠️ Tipo de reporte no válido. Por favor selecciona una opción de las tarjetas anteriores.
        </div>
      <?php endif; ?>

    </div><!-- /content -->
  </div><!-- /main -->
</div>