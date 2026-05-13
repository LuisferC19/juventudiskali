<?php
/**
 * views/pages/ReportesView.php
 * Vista para seleccionar y generar reportes PDF.
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

        <!-- Donadores -->
        <div class="card" style="padding:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="width:52px;height:52px;background:rgba(10,175,160,0.12);border-radius:16px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 16 16" fill="none" stroke="#0aafa0" stroke-width="1.5" style="width:26px;height:26px;">
              <path d="M8 2C9.1 2 10 2.9 10 4S9.1 6 8 6 6 5.1 6 4 6.9 2 8 2ZM14 13C14 10.2 11.3 9 8 9S2 10.2 2 13"/>
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
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:15px;height:15px;">
              <path d="M4 1H12V15H4Z"/><path d="M6 5H10M6 8H10M6 11H8"/>
            </svg>
            Generar PDF
          </a>
        </div>

        <!-- Beneficiarios -->
        <div class="card" style="padding:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="width:52px;height:52px;background:rgba(33,150,243,0.1);border-radius:16px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 16 16" fill="none" stroke="#2196f3" stroke-width="1.5" style="width:26px;height:26px;">
              <circle cx="6" cy="5" r="3"/>
              <path d="M1 14C1 11.2 3.2 10 6 10"/>
              <circle cx="12" cy="8" r="2.5"/>
              <path d="M9 14C9 12.1 10.3 11 12 11S15 12.1 15 14"/>
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
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:15px;height:15px;">
              <path d="M4 1H12V15H4Z"/><path d="M6 5H10M6 8H10M6 11H8"/>
            </svg>
            Generar PDF
          </a>
        </div>

        <!-- Campañas -->
        <div class="card" style="padding:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="width:52px;height:52px;background:rgba(156,39,176,0.1);border-radius:16px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 16 16" fill="none" stroke="#9c27b0" stroke-width="1.5" style="width:26px;height:26px;">
              <path d="M8 1L14 5V13H2V5Z"/><rect x="6" y="9" width="4" height="4"/>
            </svg>
          </div>
          <div>
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--texto,#1a2e2c);margin:0 0 6px;">
              Campañas
            </h3>
            <p style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;margin:0;">
              Reporte de todas las campañas con nombre, fechas, estatus y meta económica.
            </p>
          </div>
          <a href="<?= BASE_URL ?>/index.php?pagina=reportes&accion=generar&tipo=campanas"
             target="_blank"
             style="background:#9c27b0;color:#fff;text-decoration:none;border-radius:12px;padding:11px 18px;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:8px;align-self:flex-start;margin-top:auto;">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:15px;height:15px;">
              <path d="M4 1H12V15H4Z"/><path d="M6 5H10M6 8H10M6 11H8"/>
            </svg>
            Generar PDF
          </a>
        </div>

      </div><!-- /grid -->

      <!-- ══ Nota informativa ══ -->
      <div style="margin-top:28px;background:rgba(10,175,160,0.07);border:1px solid rgba(10,175,160,0.18);border-radius:16px;padding:18px 22px;font-size:13px;color:var(--muted,#5a8a84);line-height:1.7;">
        <strong style="color:var(--verde,#0aafa0);">💡 Tip:</strong>
        Los reportes se abren en una nueva pestaña del navegador. Desde ahí puedes imprimirlos o guardarlos como PDF usando la opción de impresión del navegador (Ctrl+P).
        Los datos corresponden a la información actual registrada en el sistema.
      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div>
