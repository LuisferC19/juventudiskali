<?php
/**
 * views/pages/dashboard.php
 * Dashboard principal — KPIs reales de BD + gráficas Chart.js
 */
require_once 'views/layouts/header.php';

// ── Consultas al controlador (variables pasadas desde DashboardController) ──
// $kpi_donaciones_total, $kpi_donaciones_mes, $kpi_beneficiarios,
// $kpi_campanas_activas, $kpi_inventario_items,
// $grafica_donaciones_por_mes (array), $grafica_estados_donacion (array),
// $grafica_tipo_donacion (array), $grafica_beneficiarios_estado (array),
// $actividad_reciente (array), $campanas_activas (array)
// Si las variables no existen (demo), usa valores de prueba.

$kpi_donaciones_total   = $kpi_donaciones_total   ?? '$84,200';
$kpi_donaciones_mes     = $kpi_donaciones_mes      ?? '$12,400';
$kpi_beneficiarios      = $kpi_beneficiarios       ?? 248;
$kpi_campanas_activas   = $kpi_campanas_activas    ?? 4;
$kpi_inventario_items   = $kpi_inventario_items    ?? 9;
$kpi_donadores          = $kpi_donadores           ?? 15;
$kpi_voluntarios        = $kpi_voluntarios         ?? 5;
$kpi_entregas           = $kpi_entregas            ?? 631;

// Datos para gráficas (si no vienen del controlador, datos demo)
$gd_meses  = $gd_meses  ?? json_encode(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic']);
$gd_montos = $gd_montos ?? json_encode([5000,18000,25000,42000,8000,15000,0,0,0,0,0,0]);
$gd_especie= $gd_especie?? json_encode([10,25,15,12,40,30,0,0,0,0,0,0]);

$ge_labels = $ge_labels ?? json_encode(['Verificada','Recibida','Pendiente','Rechazada']);
$ge_data   = $ge_data   ?? json_encode([9, 4, 1, 1]);

$gt_labels = $gt_labels ?? json_encode(['En especie','Económica']);
$gt_data   = $gt_data   ?? json_encode([9, 6]);

$gb_labels = $gb_labels ?? json_encode(['Activo','En espera','Inactivo']);
$gb_data   = $gb_data   ?? json_encode([13, 1, 1]);
?>

<div style="display:flex;width:100%;">

  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content" style="padding:24px;">

      <!-- ══ Bienvenida ══ -->
      <div style="margin-bottom:28px;">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;letter-spacing:-0.5px;color:var(--text,#e8eaf0);margin-bottom:4px;">
          ¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Administrador') ?>! 👋
        </h2>
        <p style="font-size:13px;color:var(--muted,#8b949e);">
          <?= date('l, j \d\e F \d\e Y') ?> · Aquí está el resumen del sistema.
        </p>
      </div>

      <!-- ══ KPIs (8 tarjetas) ══ -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:28px;">

        <div class="kpi">
          <div class="kpi-label">💰 Total donado</div>
          <div class="kpi-value"><?= $kpi_donaciones_total ?></div>
          <div class="kpi-sub">↑ 12% este mes</div>
        </div>

        <div class="kpi amber">
          <div class="kpi-label">📅 Donado este mes</div>
          <div class="kpi-value"><?= $kpi_donaciones_mes ?></div>
          <div class="kpi-sub">MXN acumulado</div>
        </div>

        <div class="kpi blue">
          <div class="kpi-label">👥 Beneficiarios</div>
          <div class="kpi-value"><?= $kpi_beneficiarios ?></div>
          <div class="kpi-sub">registrados activos</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">🚀 Campañas activas</div>
          <div class="kpi-value"><?= $kpi_campanas_activas ?></div>
          <div class="kpi-sub">en curso ahora</div>
        </div>

        <div class="kpi danger">
          <div class="kpi-label">📦 Entregas</div>
          <div class="kpi-value"><?= $kpi_entregas ?></div>
          <div class="kpi-sub">completadas</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">🤝 Donadores</div>
          <div class="kpi-value"><?= $kpi_donadores ?></div>
          <div class="kpi-sub">registrados</div>
        </div>

        <div class="kpi amber">
          <div class="kpi-label">🙋 Voluntarios</div>
          <div class="kpi-value"><?= $kpi_voluntarios ?></div>
          <div class="kpi-sub">activos</div>
        </div>

        <div class="kpi blue">
          <div class="kpi-label">📋 Inventario</div>
          <div class="kpi-value"><?= $kpi_inventario_items ?></div>
          <div class="kpi-sub">tipos de bien</div>
        </div>

      </div>

      <!-- ══ Acciones rápidas ══ -->
      <div class="quick-actions" style="margin-bottom:28px;">
        <a href="<?= BASE_URL ?>/index.php?pagina=donadores&accion=crear" class="qa-btn">
          <div class="qa-icon">🤝</div>
          <div class="qa-label">Nuevo donador</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios&accion=nuevo" class="qa-btn">
          <div class="qa-icon">👤</div>
          <div class="qa-label">Nuevo beneficiario</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=usuarios" class="qa-btn">
          <div class="qa-icon">👤</div>
          <div class="qa-label">Usuarios</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=campanas" class="qa-btn">
          <div class="qa-icon">🚀</div>
          <div class="qa-label">Campañas</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=inventario" class="qa-btn">
          <div class="qa-icon">📦</div>
          <div class="qa-label">Inventario</div>
        </a>
      </div>

      <!-- ══ Fila 1: Barras + Pastel estados ══ -->
      <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Gráfica de barras — donaciones por mes -->
        <div class="card" style="padding:24px;">
          <div class="card-header" style="margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;">Donaciones por mes (MXN vs. Especie)</h3>
            <span style="font-size:12px;color:var(--muted);">Año en curso</span>
          </div>
          <div style="height:240px;position:relative;">
            <canvas id="chartBarMeses"></canvas>
          </div>
        </div>

        <!-- Gráfica pastel — estado de donaciones -->
        <div class="card" style="padding:24px;">
          <div class="card-header" style="margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;">Estado de donaciones</h3>
          </div>
          <div style="height:200px;position:relative;display:flex;align-items:center;justify-content:center;">
            <canvas id="chartPieEstados"></canvas>
          </div>
          <div id="leyenda-estados" style="margin-top:12px;display:flex;flex-wrap:wrap;gap:8px;justify-content:center;font-size:12px;color:var(--muted);"></div>
        </div>

      </div>

      <!-- ══ Fila 2: Pastel tipos + Donut beneficiarios + Campañas + Actividad ══ -->
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Pastel tipo donación -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin-bottom:16px;">Tipo de donación</h3>
          <div style="height:180px;display:flex;align-items:center;justify-content:center;">
            <canvas id="chartPieTipo"></canvas>
          </div>
        </div>

        <!-- Donut beneficiarios por estado -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin-bottom:16px;">Beneficiarios</h3>
          <div style="height:180px;display:flex;align-items:center;justify-content:center;">
            <canvas id="chartDonutBenef"></canvas>
          </div>
        </div>

        <!-- Campañas activas con barras -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin-bottom:16px;">Campañas activas</h3>
          <div class="progress-wrap">
            <?php
            $campanas_demo = $campanas_activas ?? [
              ['nombre'=>'Abrigo Invierno',  'pct'=>72, 'color'=>'var(--primary)'],
              ['nombre'=>'Juguetes Navidad',  'pct'=>45, 'color'=>'var(--amber)'],
              ['nombre'=>'Empleo Joven',      'pct'=>18, 'color'=>'var(--blue,#3b6fd4)'],
              ['nombre'=>'Medicina Todos',    'pct'=>8,  'color'=>'var(--danger)'],
            ];
            foreach ($campanas_demo as $c): ?>
              <div class="prog-item">
                <div class="prog-label">
                  <span style="font-size:12px;"><?= htmlspecialchars($c['nombre']) ?></span>
                  <span style="color:var(--accent);font-size:12px;font-weight:600;"><?= $c['pct'] ?>%</span>
                </div>
                <div class="prog-bar">
                  <div class="prog-fill" style="width:<?= $c['pct'] ?>%;background:<?= $c['color'] ?>"></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Actividad reciente -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin-bottom:16px;">Actividad reciente</h3>
          <?php
          $actividad_demo = $actividad_reciente ?? [
            ['dot'=>'#1A7A5E','texto'=>'Donación registrada','tiempo'=>'5 min'],
            ['dot'=>'#3B6FD4','texto'=>'Entrega completada #142','tiempo'=>'1 h'],
            ['dot'=>'#1A7A5E','texto'=>'Nuevo beneficiario','tiempo'=>'2 h'],
            ['dot'=>'#C68B0A','texto'=>'Stock bajo — Medicamentos','tiempo'=>'3 h'],
            ['dot'=>'#993556','texto'=>'Nueva queja recibida','tiempo'=>'5 h'],
          ];
          foreach ($actividad_demo as $a): ?>
            <div class="activity-item">
              <div class="activity-dot" style="background:<?= htmlspecialchars($a['dot']) ?>"></div>
              <div class="activity-text" style="font-size:12px;"><?= htmlspecialchars($a['texto']) ?></div>
              <div class="activity-time"><?= htmlspecialchars($a['tiempo']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  // Detectar si el sistema usa tema oscuro
  const isDark = document.body.classList.contains('dark') || 
                 getComputedStyle(document.documentElement).getPropertyValue('--dark') !== '';

  const gridColor  = 'rgba(255,255,255,0.06)';
  const textColor  = '#8b949e';
  const colVerde   = '#0aafa0';
  const colAmber   = '#e8a020';
  const colBlue    = '#3b6fd4';
  const colDanger  = '#c0392b';
  const colPurple  = '#9b59b6';

  Chart.defaults.color      = textColor;
  Chart.defaults.font.family = "'DM Sans', sans-serif";
  Chart.defaults.font.size  = 12;

  /* ── Barras — donaciones por mes ── */
  const ctxBar = document.getElementById('chartBarMeses').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: <?= $gd_meses ?>,
      datasets: [
        {
          label: 'MXN',
          data:  <?= $gd_montos ?>,
          backgroundColor: colVerde + 'cc',
          borderRadius: 6,
          borderSkipped: false,
        },
        {
          label: 'Especie (unidades)',
          data:  <?= $gd_especie ?>,
          backgroundColor: colAmber + 'aa',
          borderRadius: 6,
          borderSkipped: false,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top', labels: { boxWidth: 12, padding: 16 } },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        x: { grid: { color: gridColor }, ticks: { color: textColor } },
        y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true }
      }
    }
  });

  /* ── Pastel — estados de donación ── */
  const ctxPieEst = document.getElementById('chartPieEstados').getContext('2d');
  const labEstados = <?= $ge_labels ?>;
  const datEstados = <?= $ge_data ?>;
  const pieChart = new Chart(ctxPieEst, {
    type: 'pie',
    data: {
      labels: labEstados,
      datasets: [{
        data: datEstados,
        backgroundColor: [colVerde, colAmber, colBlue, colDanger],
        borderWidth: 2,
        borderColor: '#1c2430'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
      }
    }
  });

  // Leyenda manual más bonita
  const legEl = document.getElementById('leyenda-estados');
  const colors = [colVerde, colAmber, colBlue, colDanger];
  labEstados.forEach((l, i) => {
    legEl.innerHTML += `<span style="display:flex;align-items:center;gap:4px;">
      <span style="width:10px;height:10px;background:${colors[i]};border-radius:50%;display:inline-block;"></span>
      ${l} (${datEstados[i]})
    </span>`;
  });

  /* ── Pastel — tipo de donación ── */
  const ctxPieTipo = document.getElementById('chartPieTipo').getContext('2d');
  new Chart(ctxPieTipo, {
    type: 'doughnut',
    data: {
      labels: <?= $gt_labels ?>,
      datasets: [{
        data: <?= $gt_data ?>,
        backgroundColor: [colVerde, colBlue],
        borderWidth: 2,
        borderColor: '#1c2430'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8 } }
      }
    }
  });

  /* ── Donut — beneficiarios por estado ── */
  const ctxDonut = document.getElementById('chartDonutBenef').getContext('2d');
  new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
      labels: <?= $gb_labels ?>,
      datasets: [{
        data: <?= $gb_data ?>,
        backgroundColor: [colVerde, colAmber, '#666'],
        borderWidth: 2,
        borderColor: '#1c2430'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '65%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8 } }
      }
    }
  });

})();
</script>

<?php require_once 'views/layouts/footer.php'; ?>