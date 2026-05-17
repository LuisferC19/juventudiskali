<?php
/**
 * views/pages/DashboardView.php
 * Dashboard con 6 gráficas dinámicas + KPIs + acciones rápidas.
 *
 * Gráficas incluidas:
 *  1. Barras — Donaciones por mes (MXN vs Especie)
 *  2. Pastel — Estado de donaciones
 *  3. Donut  — Tipo de donación (económica / especie)
 *  4. Donut  — Beneficiarios por estado (activo / en espera / inactivo)
 *  5. Barras horiz. — Tipos de donador (anónimo / persona / grupo / organización)
 *  6. Barras horiz. — Usuarios por rol
 *
 * Todas usan datos REALES de la BD; cuando no hay datos muestran
 * datos estáticos ilustrativos con una etiqueta "Demo".
 */
require_once 'views/layouts/header.php';

// Fallbacks seguros (por si el controller no asignó la variable)
$kpi_donaciones_total ??= '$0.00';
$kpi_donaciones_mes   ??= '$0.00';
$kpi_beneficiarios    ??= 0;
$kpi_campanas_activas ??= 0;
$kpi_inventario_items ??= 0;
$kpi_donadores        ??= 0;
$kpi_voluntarios      ??= 0;
$kpi_entregas         ??= 0;

$gd_meses   ??= json_encode(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic']);
$gd_montos  ??= json_encode([0,0,5200,18000,25000,42000,0,0,0,0,0,0]);
$gd_especie ??= json_encode([0,0,10,25,15,30,0,0,0,0,0,0]);
$gd_demo    ??= true;

$ge_labels ??= json_encode(['Verificada','Recibida','Pendiente','Rechazada']);
$ge_data   ??= json_encode([9,4,1,1]);
$ge_demo   ??= true;

$gt_labels ??= json_encode(['Económica','En especie']);
$gt_data   ??= json_encode([6,9]);
$gt_demo   ??= true;

$gb_labels ??= json_encode(['Activos','En espera','Inactivos']);
$gb_data   ??= json_encode([13,3,2]);
$gb_demo   ??= true;

$gdon_labels ??= json_encode(['Anónimo','Persona','Grupo','Organización']);
$gdon_data   ??= json_encode([2,8,3,5]);
$gdon_demo   ??= true;

$grol_labels ??= json_encode(['Administrador','Coordinador','Voluntario','Donador','Inventarista']);
$grol_data   ??= json_encode([2,1,4,6,1]);
$grol_demo   ??= true;

$campanas_activas ??= [];

// Helper para etiqueta "Demo"
function demoBadge(bool $esDemo): string {
    if (!$esDemo) return '';
    return '<span style="font-size:10px;background:rgba(232,160,32,0.18);color:#e8a020;
                         border-radius:4px;padding:2px 7px;margin-left:8px;font-weight:600;
                         letter-spacing:.5px;">DEMO</span>';
}
?>

<div style="display:flex;width:100%;">

  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content" style="padding:24px;">

      <!-- ══ Bienvenida ══ -->
      <div style="margin-bottom:28px;">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;
                   letter-spacing:-0.5px;color:var(--text,#e8eaf0);margin-bottom:4px;">
          ¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Administrador') ?>!
          <img src="<?= BASE_URL ?>/public/iconos/Saludar.png" alt="👋"
               style="width:20px;height:20px;vertical-align:middle;margin-left:4px;">
        </h2>
        <p style="font-size:13px;color:var(--muted,#8b949e);">
          <?= date('l, j \d\e F \d\e Y') ?> · Aquí está el resumen del sistema.
        </p>
      </div>

      <!-- ══ KPIs ══ -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:16px;margin-bottom:28px;">

        <div class="kpi">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/Total_dinero.png" alt="" style="width:16px;height:16px;">
            Total donado
          </div>
          <div class="kpi-value"><?= $kpi_donaciones_total ?></div>
          <div class="kpi-sub">acumulado histórico</div>
        </div>

        <div class="kpi amber">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/calendario.png" alt="" style="width:16px;height:16px;">
            Este mes
          </div>
          <div class="kpi-value"><?= $kpi_donaciones_mes ?></div>
          <div class="kpi-sub">MXN donado</div>
        </div>

        <div class="kpi blue">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/Beneficiarios.png" alt="" style="width:16px;height:16px;">
            Beneficiarios
          </div>
          <div class="kpi-value"><?= $kpi_beneficiarios ?></div>
          <div class="kpi-sub">activos registrados</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">🚀 Campañas</div>
          <div class="kpi-value"><?= $kpi_campanas_activas ?></div>
          <div class="kpi-sub">activas ahora</div>
        </div>

        <div class="kpi danger">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/Entregas.png" alt="" style="width:16px;height:16px;">
            Entregas
          </div>
          <div class="kpi-value"><?= $kpi_entregas ?></div>
          <div class="kpi-sub">completadas</div>
        </div>

        <div class="kpi">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/patrocinador.png" alt="" style="width:16px;height:16px;">
            Donadores
          </div>
          <div class="kpi-value"><?= $kpi_donadores ?></div>
          <div class="kpi-sub">registrados</div>
        </div>

        <div class="kpi amber">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/Voluntarios.png" alt="" style="width:16px;height:16px;">
            Voluntarios
          </div>
          <div class="kpi-value"><?= $kpi_voluntarios ?></div>
          <div class="kpi-sub">activos</div>
        </div>

        <div class="kpi blue">
          <div class="kpi-label">
            <img src="<?= BASE_URL ?>/public/iconos/inventario.png" alt="" style="width:16px;height:16px;">
            Inventario
          </div>
          <div class="kpi-value"><?= $kpi_inventario_items ?></div>
          <div class="kpi-sub">tipos de bien</div>
        </div>

      </div>

      <!-- ══ Acciones rápidas ══ -->
      <div class="quick-actions" style="margin-bottom:28px;">
        <a href="<?= BASE_URL ?>/index.php?pagina=donadores&accion=crear" class="qa-btn">
          <div class="qa-icon"><img src="<?= BASE_URL ?>/public/iconos/New_donador.png" alt="" style="width:18px;height:18px;"></div>
          <div class="qa-label">Nuevo donador</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios&accion=nuevo" class="qa-btn">
          <div class="qa-icon"><img src="<?= BASE_URL ?>/public/iconos/New_beneficiario.png" alt="" style="width:18px;height:18px;"></div>
          <div class="qa-label">Nuevo beneficiario</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=usuarios" class="qa-btn">
          <div class="qa-icon"><img src="<?= BASE_URL ?>/public/iconos/New_Usuario.png" alt="" style="width:18px;height:18px;"></div>
          <div class="qa-label">Usuarios</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=campanas" class="qa-btn">
          <div class="qa-icon">🚀</div>
          <div class="qa-label">Campañas</div>
        </a>
        <a href="<?= BASE_URL ?>/index.php?pagina=inventario" class="qa-btn">
          <div class="qa-icon"><img src="<?= BASE_URL ?>/public/iconos/inventario.png" alt="" style="width:18px;height:18px;"></div>
          <div class="qa-label">Inventario</div>
        </a>
      </div>

      <!-- ══ FILA 1: Barras donaciones + Estado donaciones ══ -->
      <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Barras: donaciones por mes -->
        <div class="card" style="padding:24px;">
          <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <h3 style="font-size:15px;font-weight:600;margin:0;">
              Donaciones por mes (MXN vs Especie)
              <?= demoBadge($gd_demo) ?>
            </h3>
            <span style="font-size:12px;color:var(--muted);">Año <?= date('Y') ?></span>
          </div>
          <div style="height:240px;position:relative;">
            <canvas id="chartBarMeses"></canvas>
          </div>
        </div>

        <!-- Pastel: estado de donaciones -->
        <div class="card" style="padding:24px;">
          <div class="card-header" style="margin-bottom:16px;">
            <h3 style="font-size:15px;font-weight:600;margin:0;">
              Estado de donaciones
              <?= demoBadge($ge_demo) ?>
            </h3>
          </div>
          <div style="height:185px;display:flex;align-items:center;justify-content:center;">
            <canvas id="chartPieEstados"></canvas>
          </div>
          <div id="leyenda-estados" style="margin-top:10px;display:flex;flex-wrap:wrap;gap:6px;
               justify-content:center;font-size:11px;color:var(--muted);"></div>
        </div>

      </div>

      <!-- ══ FILA 2: Tipo donación + Beneficiarios + Campañas + Actividad ══ -->
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Donut: tipo de donación -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin:0 0 14px;">
            Tipo de donación
            <?= demoBadge($gt_demo) ?>
          </h3>
          <div style="height:175px;display:flex;align-items:center;justify-content:center;">
            <canvas id="chartPieTipo"></canvas>
          </div>
        </div>

        <!-- Donut: beneficiarios por estado -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin:0 0 14px;">
            Beneficiarios
            <?= demoBadge($gb_demo) ?>
          </h3>
          <div style="height:175px;display:flex;align-items:center;justify-content:center;">
            <canvas id="chartDonutBenef"></canvas>
          </div>
        </div>

        <!-- Campañas activas -->
        <div class="card" style="padding:20px;">
          <h3 style="font-size:14px;font-weight:600;margin:0 0 14px;">Campañas activas</h3>
          <div class="progress-wrap">
            <?php
            $campanas_show = !empty($campanas_activas) ? $campanas_activas : [
                ['nombre'=>'Abrigo Invierno',  'pct'=>72, 'color'=>'var(--primary)'],
                ['nombre'=>'Juguetes Navidad',  'pct'=>45, 'color'=>'var(--amber)'],
                ['nombre'=>'Empleo Joven',      'pct'=>18, 'color'=>'#3b6fd4'],
                ['nombre'=>'Medicina Todos',    'pct'=>8,  'color'=>'var(--danger)'],
            ];
            foreach ($campanas_show as $c): ?>
              <div class="prog-item">
                <div class="prog-label">
                  <span style="font-size:12px;"><?= htmlspecialchars($c['nombre']) ?></span>
                  <span style="color:var(--accent,#0aafa0);font-size:12px;font-weight:600;"><?= $c['pct'] ?>%</span>
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
          <h3 style="font-size:14px;font-weight:600;margin:0 0 14px;">Actividad reciente</h3>
          <?php
          $actividad = [
              ['dot'=>'#1A7A5E','texto'=>'Donación registrada',       'tiempo'=>'5 min'],
              ['dot'=>'#3B6FD4','texto'=>'Entrega completada #142',    'tiempo'=>'1 h'],
              ['dot'=>'#1A7A5E','texto'=>'Nuevo beneficiario',          'tiempo'=>'2 h'],
              ['dot'=>'#C68B0A','texto'=>'Stock bajo — Medicamentos',   'tiempo'=>'3 h'],
              ['dot'=>'#993556','texto'=>'Nuevo donador registrado',    'tiempo'=>'5 h'],
          ];
          foreach ($actividad as $a): ?>
            <div class="activity-item">
              <div class="activity-dot" style="background:<?= $a['dot'] ?>"></div>
              <div class="activity-text" style="font-size:12px;"><?= htmlspecialchars($a['texto']) ?></div>
              <div class="activity-time"><?= htmlspecialchars($a['tiempo']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>

      <!-- ══ FILA 3: Tipos de donador + Usuarios por rol ══ -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Barras horiz: tipos de donador -->
        <div class="card" style="padding:24px;">
          <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <h3 style="font-size:15px;font-weight:600;margin:0;">
              <img src="<?= BASE_URL ?>/public/iconos/patrocinador.png" alt=""
                   style="width:16px;height:16px;vertical-align:middle;margin-right:6px;">
              Tipos de donador
              <?= demoBadge($gdon_demo) ?>
            </h3>
          </div>
          <div style="height:200px;position:relative;">
            <canvas id="chartDonadorTipo"></canvas>
          </div>
        </div>

        <!-- Barras horiz: usuarios por rol -->
        <div class="card" style="padding:24px;">
          <div class="card-header" style="margin-bottom:16px;">
            <h3 style="font-size:15px;font-weight:600;margin:0;">
              <img src="<?= BASE_URL ?>/public/iconos/Usuarios.png" alt=""
                   style="width:16px;height:16px;vertical-align:middle;margin-right:6px;">
              Usuarios por rol
              <?= demoBadge($grol_demo) ?>
            </h3>
          </div>
          <div style="height:200px;position:relative;">
            <canvas id="chartRolUsuarios"></canvas>
          </div>
        </div>

      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<!-- ══ Chart.js ══ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {

  /* ── Colores globales ── */
  const gridColor = 'rgba(255,255,255,0.06)';
  const textColor = '#8b949e';
  const colVerde  = '#0aafa0';
  const colAmber  = '#e8a020';
  const colBlue   = '#3b6fd4';
  const colRed    = '#c0392b';
  const colPurple = '#9b59b6';
  const colGray   = '#555e6e';

  Chart.defaults.color       = textColor;
  Chart.defaults.font.family = "'DM Sans', sans-serif";
  Chart.defaults.font.size   = 12;

  /* ══════════════════════════════════════════════════
     1. BARRAS — Donaciones por mes
  ══════════════════════════════════════════════════ */
  new Chart(document.getElementById('chartBarMeses'), {
    type: 'bar',
    data: {
      labels:   <?= $gd_meses ?>,
      datasets: [
        {
          label: 'MXN',
          data:  <?= $gd_montos ?>,
          backgroundColor: colVerde + 'cc',
          borderRadius: 5,
          borderSkipped: false,
        },
        {
          label: 'Especie (unidades)',
          data:  <?= $gd_especie ?>,
          backgroundColor: colAmber + 'aa',
          borderRadius: 5,
          borderSkipped: false,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top', labels: { boxWidth: 12, padding: 14 } },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        x: { grid: { color: gridColor }, ticks: { color: textColor } },
        y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true }
      }
    }
  });

  /* ══════════════════════════════════════════════════
     2. PASTEL — Estado de donaciones
  ══════════════════════════════════════════════════ */
  const labEstados = <?= $ge_labels ?>;
  const datEstados = <?= $ge_data ?>;
  const colsEstados = [colVerde, colAmber, colBlue, colRed];

  new Chart(document.getElementById('chartPieEstados'), {
    type: 'pie',
    data: {
      labels: labEstados,
      datasets: [{
        data: datEstados,
        backgroundColor: colsEstados,
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

  // Leyenda manual
  const legEl = document.getElementById('leyenda-estados');
  labEstados.forEach((l, i) => {
    legEl.innerHTML += `<span style="display:flex;align-items:center;gap:4px;">
      <span style="width:9px;height:9px;background:${colsEstados[i]};border-radius:50%;display:inline-block;"></span>
      ${l} (${datEstados[i]})
    </span>`;
  });

  /* ══════════════════════════════════════════════════
     3. DONUT — Tipo de donación
  ══════════════════════════════════════════════════ */
  new Chart(document.getElementById('chartPieTipo'), {
    type: 'doughnut',
    data: {
      labels: <?= $gt_labels ?>,
      datasets: [{
        data: <?= $gt_data ?>,
        backgroundColor: [colBlue, colVerde],
        borderWidth: 2,
        borderColor: '#1c2430'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '62%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8 } }
      }
    }
  });

  /* ══════════════════════════════════════════════════
     4. DONUT — Beneficiarios por estado
  ══════════════════════════════════════════════════ */
  new Chart(document.getElementById('chartDonutBenef'), {
    type: 'doughnut',
    data: {
      labels: <?= $gb_labels ?>,
      datasets: [{
        data: <?= $gb_data ?>,
        backgroundColor: [colVerde, colAmber, colGray],
        borderWidth: 2,
        borderColor: '#1c2430'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '62%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8 } }
      }
    }
  });

  /* ══════════════════════════════════════════════════
     5. BARRAS HORIZONTALES — Tipos de donador
  ══════════════════════════════════════════════════ */
  new Chart(document.getElementById('chartDonadorTipo'), {
    type: 'bar',
    data: {
      labels: <?= $gdon_labels ?>,
      datasets: [{
        label: 'Donadores',
        data:  <?= $gdon_data ?>,
        backgroundColor: [colGray, colVerde, colBlue, colPurple],
        borderRadius: 5,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.x} donador(es)` } }
      },
      scales: {
        x: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true,
             ticks: { precision: 0, color: textColor } },
        y: { grid: { display: false }, ticks: { color: textColor } }
      }
    }
  });

  /* ══════════════════════════════════════════════════
     6. BARRAS HORIZONTALES — Usuarios por rol
  ══════════════════════════════════════════════════ */
  const rolLabels = <?= $grol_labels ?>;
  const rolData   = <?= $grol_data ?>;

  // Paleta de colores según posición
  const rolColors = [colVerde, colBlue, colAmber, colPurple, colRed, colGray,
                     '#26c6da','#ef6c00','#8d6e63','#546e7a'];

  new Chart(document.getElementById('chartRolUsuarios'), {
    type: 'bar',
    data: {
      labels: rolLabels,
      datasets: [{
        label: 'Usuarios',
        data:  rolData,
        backgroundColor: rolColors.slice(0, rolLabels.length),
        borderRadius: 5,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.x} usuario(s)` } }
      },
      scales: {
        x: { grid: { color: gridColor },
             ticks: { precision: 0, color: textColor }, beginAtZero: true },
        y: { grid: { display: false }, ticks: { color: textColor } }
      }
    }
  });

})();
</script>

<?php require_once 'views/layouts/footer.php'; ?>
