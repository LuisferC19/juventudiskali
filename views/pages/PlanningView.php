<?php
/**
 * views/pages/PlanningView.php
 * Módulo de Planning & Actividades — interfaz rediseñada.
 */
require_once 'views/layouts/header.php';

// Helpers de estado
$estadoMeta = [
    'planeada'   => ['label' => 'Planeada',   'color' => '#6366f1', 'bg' => '#eef2ff', 'dot' => '#6366f1'],
    'en_curso'   => ['label' => 'En curso',   'color' => '#d97706', 'bg' => '#fffbeb', 'dot' => '#f59e0b'],
    'completada' => ['label' => 'Completada', 'color' => '#059669', 'bg' => '#ecfdf5', 'dot' => '#10b981'],
    'cancelada'  => ['label' => 'Cancelada',  'color' => '#dc2626', 'bg' => '#fef2f2', 'dot' => '#ef4444'],
];

$zonaMeta = [
    'san_martin' => ['label' => 'San Martín', 'color' => '#0891b2', 'bg' => '#e0f2fe'],
    'tlaxcala'   => ['label' => 'Tlaxcala',   'color' => '#7c3aed', 'bg' => '#f5f3ff'],
    'ambas'      => ['label' => 'Ambas',       'color' => '#0aafa0', 'bg' => '#f0fdfa'],
];

$meses_es = [
    1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
    7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
];
$nombre_mes = $meses_es[$mes_actual] ?? 'Mes';
$csrf = csrfToken();
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content" style="padding:24px 28px;background:#f8fffe;min-height:calc(100vh - 60px);">

      <!-- ══ Encabezado ══════════════════════════════════════════════════════ -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#1a2e2c;margin:0 0 4px;">
            Planning & Actividades
          </h2>
          <p style="font-size:13px;color:#5a8a84;margin:0;">
            <?= $nombre_mes ?> <?= $anio_actual ?> — gestión de actividades por zona
          </p>
        </div>
        <button onclick="abrirModal()" style="display:inline-flex;align-items:center;gap:8px;background:#0aafa0;color:#fff;border:none;border-radius:12px;padding:10px 20px;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;box-shadow:0 4px 14px rgba(10,175,160,.3);"
          onmouseover="this.style.background='#057a6f'" onmouseout="this.style.background='#0aafa0'">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Nueva actividad
        </button>
      </div>

      <!-- ══ KPI Cards ════════════════════════════════════════════════════════ -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">

        <div style="background:#fff;border-radius:16px;border:1px solid #e0f2f0;padding:18px 20px;position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#0aafa0;border-radius:4px 0 0 4px;"></div>
          <div style="font-size:11px;font-weight:600;color:#5a8a84;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Total actividades</div>
          <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#1a2e2c;line-height:1;"><?= (int)$kpis['total_actividades'] ?></div>
          <div style="font-size:11px;color:#0aafa0;margin-top:4px;">registradas</div>
        </div>

        <div style="background:#fff;border-radius:16px;border:1px solid #e0f2f0;padding:18px 20px;position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#f59e0b;border-radius:4px 0 0 4px;"></div>
          <div style="font-size:11px;font-weight:600;color:#5a8a84;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Esta semana</div>
          <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#1a2e2c;line-height:1;"><?= (int)$kpis['actividades_semana'] ?></div>
          <div style="font-size:11px;color:#f59e0b;margin-top:4px;">programadas</div>
        </div>

        <div style="background:#fff;border-radius:16px;border:1px solid #e0f2f0;padding:18px 20px;position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#0891b2;border-radius:4px 0 0 4px;"></div>
          <div style="font-size:11px;font-weight:600;color:#5a8a84;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">San Martín</div>
          <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#1a2e2c;line-height:1;"><?= (int)$kpis['san_martin'] ?></div>
          <div style="font-size:11px;color:#0891b2;margin-top:4px;">actividades</div>
        </div>

        <div style="background:#fff;border-radius:16px;border:1px solid #e0f2f0;padding:18px 20px;position:relative;overflow:hidden;">
          <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#7c3aed;border-radius:4px 0 0 4px;"></div>
          <div style="font-size:11px;font-weight:600;color:#5a8a84;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Tlaxcala</div>
          <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:700;color:#1a2e2c;line-height:1;"><?= (int)$kpis['tlaxcala'] ?></div>
          <div style="font-size:11px;color:#7c3aed;margin-top:4px;">actividades</div>
        </div>

      </div>

      <!-- ══ Layout: Calendario + Lista ══════════════════════════════════════ -->
      <div style="display:grid;grid-template-columns:1fr 360px;gap:18px;align-items:start;">

        <!-- ─ Calendario ─────────────────────────────────────────────────── -->
        <div style="background:#fff;border-radius:20px;border:1px solid #e0f2f0;overflow:hidden;">

          <!-- Cabecera del calendario -->
          <div style="padding:18px 22px;border-bottom:1px solid #e0f2f0;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0aafa0" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#1a2e2c;">
                <?= $nombre_mes ?> <?= $anio_actual ?>
              </span>
            </div>
            <div style="display:flex;gap:6px;">
              <a href="?pagina=planning&mes=<?= $mes_actual > 1 ? $mes_actual-1 : 12 ?>&anio=<?= $mes_actual > 1 ? $anio_actual : $anio_actual-1 ?>"
                 style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#f0fdfa;border:1px solid #c8efeb;color:#0aafa0;text-decoration:none;font-size:14px;font-weight:700;transition:background .2s;" title="Mes anterior">‹</a>
              <a href="?pagina=planning&mes=<?= date('m') ?>&anio=<?= date('Y') ?>"
                 style="display:inline-flex;align-items:center;padding:0 12px;height:32px;border-radius:8px;background:#f0fdfa;border:1px solid #c8efeb;color:#0aafa0;text-decoration:none;font-size:11px;font-weight:700;">Hoy</a>
              <a href="?pagina=planning&mes=<?= $mes_actual < 12 ? $mes_actual+1 : 1 ?>&anio=<?= $mes_actual < 12 ? $anio_actual : $anio_actual+1 ?>"
                 style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#f0fdfa;border:1px solid #c8efeb;color:#0aafa0;text-decoration:none;font-size:14px;font-weight:700;">›</a>
            </div>
          </div>

          <!-- Grid de días -->
          <div style="padding:16px 18px;">

            <!-- Encabezados de días -->
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:6px;">
              <?php foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d): ?>
                <div style="text-align:center;font-size:10.5px;font-weight:700;color:#5a8a84;text-transform:uppercase;letter-spacing:.06em;padding:6px 0;"><?= $d ?></div>
              <?php endforeach; ?>
            </div>

            <!-- Días del mes -->
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;">
              <?php
                $primer_dia   = mktime(0,0,0,$mes_actual,1,$anio_actual);
                $ultimo_dia   = mktime(0,0,0,$mes_actual+1,0,$anio_actual);
                $num_dias     = (int)date('d',$ultimo_dia);
                $inicio_sem   = (int)date('N',$primer_dia); // 1=Lun,7=Dom
                $hoy_dia      = (date('m')==$mes_actual && date('Y')==$anio_actual) ? (int)date('d') : 0;

                // Celdas vacías iniciales
                for ($i=1; $i<$inicio_sem; $i++):
              ?>
                <div style="min-height:72px;border-radius:10px;background:#f8fffe;"></div>
              <?php endfor;

                // Días del mes
                for ($dia=1; $dia<=$num_dias; $dia++):
                  $tiene = isset($actividades_mes[$dia]) && count($actividades_mes[$dia]) > 0;
                  $esHoy = ($dia === $hoy_dia);
              ?>
                <div style="min-height:72px;border-radius:10px;padding:7px;cursor:pointer;transition:all .15s;border:1px solid <?= $esHoy ? '#0aafa0' : ($tiene ? '#c8efeb' : '#f0fdfa') ?>;background:<?= $esHoy ? '#f0fdfa' : ($tiene ? '#f7fffe' : '#fafffe') ?>;"
                     onclick="verDia(<?= $dia ?>)"
                     onmouseover="this.style.background='#e8f9f7';this.style.borderColor='#0aafa0'"
                     onmouseout="this.style.background='<?= $esHoy ? '#f0fdfa' : ($tiene ? '#f7fffe' : '#fafffe') ?>'; this.style.borderColor='<?= $esHoy ? '#0aafa0' : ($tiene ? '#c8efeb' : '#f0fdfa') ?>'">

                  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-family:'Syne',sans-serif;font-size:12px;font-weight:<?= $esHoy ? '800' : '600' ?>;color:<?= $esHoy ? '#0aafa0' : '#1a2e2c' ?>;"><?= $dia ?></span>
                    <?php if ($tiene): ?>
                      <span style="background:#0aafa0;color:#fff;font-size:9px;font-weight:700;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;"><?= count($actividades_mes[$dia]) ?></span>
                    <?php endif; ?>
                  </div>

                  <?php if ($tiene): ?>
                    <?php foreach (array_slice($actividades_mes[$dia], 0, 2) as $act):
                      $em = $estadoMeta[$act['estado']] ?? $estadoMeta['planeada'];
                    ?>
                      <div style="background:<?= $em['bg'] ?>;border-left:2px solid <?= $em['color'] ?>;border-radius:4px;padding:2px 5px;margin-bottom:2px;overflow:hidden;">
                        <span style="font-size:9px;font-weight:600;color:<?= $em['color'] ?>;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                          <?= htmlspecialchars(mb_substr($act['titulo'], 0, 14)) ?>
                        </span>
                      </div>
                    <?php endforeach; ?>
                    <?php if (count($actividades_mes[$dia]) > 2): ?>
                      <div style="font-size:9px;color:#5a8a84;text-align:center;">+<?= count($actividades_mes[$dia])-2 ?> más</div>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              <?php endfor;

                // Celdas vacías finales
                $dias_finales = (7 - (($num_dias + $inicio_sem - 1) % 7)) % 7;
                for ($i=0; $i<$dias_finales; $i++):
              ?>
                <div style="min-height:72px;border-radius:10px;background:#f8fffe;"></div>
              <?php endfor; ?>
            </div>

          </div>

          <!-- Leyenda de estados -->
          <div style="padding:12px 18px 16px;border-top:1px solid #f0fdfa;display:flex;gap:16px;flex-wrap:wrap;">
            <?php foreach($estadoMeta as $estado => $meta): ?>
              <div style="display:flex;align-items:center;gap:5px;">
                <div style="width:8px;height:8px;border-radius:50%;background:<?= $meta['dot'] ?>;"></div>
                <span style="font-size:11px;color:#5a8a84;"><?= $meta['label'] ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ─ Panel lateral: Filtros + Lista ────────────────────────────── -->
        <div style="display:flex;flex-direction:column;gap:14px;">

          <!-- Filtro de zona -->
          <div style="background:#fff;border-radius:16px;border:1px solid #e0f2f0;padding:16px 18px;">
            <div style="font-size:11px;font-weight:700;color:#5a8a84;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Filtrar por zona</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              <?php
                $zona_actual = $_GET['zona'] ?? 'todas';
                $zonas_filtro = ['todas'=>['label'=>'Todas','color'=>'#0aafa0','bg'=>'#f0fdfa']] + array_map(fn($z)=>$z, $zonaMeta);
              ?>
              <?php foreach($zonas_filtro as $k => $z): ?>
                <a href="?pagina=planning&mes=<?= $mes_actual ?>&anio=<?= $anio_actual ?>&zona=<?= $k ?>"
                   style="display:inline-flex;align-items:center;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:600;text-decoration:none;border:1.5px solid <?= $zona_actual===$k ? $z['color'] : '#e0f2f0' ?>;background:<?= $zona_actual===$k ? $z['bg'] : '#fff' ?>;color:<?= $zona_actual===$k ? $z['color'] : '#5a8a84' ?>;transition:all .15s;">
                  <?= $z['label'] ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Lista de actividades próximas -->
          <div style="background:#fff;border-radius:16px;border:1px solid #e0f2f0;overflow:hidden;">
            <div style="padding:14px 18px;border-bottom:1px solid #f0fdfa;">
              <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#1a2e2c;">Actividades del mes</span>
              <span style="margin-left:8px;background:#f0fdfa;color:#0aafa0;font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;"><?= count($actividades) ?></span>
            </div>

            <div style="max-height:420px;overflow-y:auto;">
              <?php if (empty($actividades)): ?>
                <div style="padding:32px;text-align:center;color:#5a8a84;font-size:13px;">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c8efeb" stroke-width="1.5" style="display:block;margin:0 auto 10px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  Sin actividades registradas
                </div>
              <?php else: ?>
                <?php foreach($actividades as $act):
                  $em = $estadoMeta[$act['estado']] ?? $estadoMeta['planeada'];
                  $zm = $zonaMeta[$act['zona']] ?? $zonaMeta['ambas'];
                  $fi = date('d/m', strtotime($act['fecha_inicio']));
                  $ff = date('d/m', strtotime($act['fecha_fin']));
                ?>
                  <div style="padding:12px 18px;border-bottom:1px solid #f8fffe;transition:background .15s;"
                       onmouseover="this.style.background='#f8fffe'" onmouseout="this.style.background='#fff'">
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                      <div style="width:3px;min-height:40px;border-radius:3px;background:<?= $em['dot'] ?>;flex-shrink:0;margin-top:2px;"></div>
                      <div style="flex:1;min-width:0;">
                        <div style="font-size:12.5px;font-weight:600;color:#1a2e2c;margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                          <?= htmlspecialchars($act['titulo']) ?>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                          <span style="font-size:10px;font-weight:600;color:<?= $em['color'] ?>;background:<?= $em['bg'] ?>;padding:2px 7px;border-radius:99px;"><?= $em['label'] ?></span>
                          <span style="font-size:10px;font-weight:600;color:<?= $zm['color'] ?>;background:<?= $zm['bg'] ?>;padding:2px 7px;border-radius:99px;"><?= $zm['label'] ?></span>
                          <span style="font-size:10px;color:#5a8a84;"><?= $fi ?><?= $fi !== $ff ? " → {$ff}" : '' ?></span>
                        </div>
                        <?php if (!empty($act['nombre'])): ?>
                          <div style="font-size:10px;color:#5a8a84;margin-top:4px;">
                            👤 <?= htmlspecialchars($act['nombre'].' '.$act['apellido']) ?>
                          </div>
                        <?php endif; ?>
                      </div>
                      <!-- Selector de estado inline -->
                      <select onchange="cambiarEstado(<?= (int)$act['id_actividad'] ?>, this.value)"
                              style="font-size:10px;border:1px solid #e0f2f0;border-radius:6px;padding:3px 6px;color:#5a8a84;background:#fff;cursor:pointer;max-width:90px;flex-shrink:0;">
                        <?php foreach($estadoMeta as $ek => $ev): ?>
                          <option value="<?= $ek ?>" <?= $act['estado']===$ek ? 'selected' : '' ?>><?= $ev['label'] ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

        </div><!-- /panel lateral -->
      </div><!-- /grid principal -->

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<!-- ══ Modal: Nueva actividad ══════════════════════════════════════════════ -->
<div id="modal-planning" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center;">
  <!-- Overlay -->
  <div onclick="cerrarModal()" style="position:absolute;inset:0;background:rgba(10,30,28,.45);backdrop-filter:blur(4px);"></div>

  <!-- Panel -->
  <div style="position:relative;z-index:1001;background:#fff;border-radius:20px;width:90%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(10,175,160,.18);border:1px solid #c8efeb;">

    <!-- Header del modal -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f0fdfa;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:36px;height:36px;border-radius:10px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0aafa0" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#1a2e2c;">Nueva actividad</div>
          <div style="font-size:11px;color:#5a8a84;">Completa los datos de la actividad</div>
        </div>
      </div>
      <button onclick="cerrarModal()" style="width:30px;height:30px;border-radius:8px;border:1px solid #e0f2f0;background:#f8fffe;color:#5a8a84;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">×</button>
    </div>

    <!-- Formulario -->
    <form id="form-actividad" onsubmit="guardarActividad(event)" style="padding:22px 24px;display:flex;flex-direction:column;gap:16px;">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

      <!-- Título -->
      <div>
        <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">
          Título <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="titulo" required maxlength="150"
               placeholder="Ej: Reforestación en parque central"
               style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;"
               onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'">
      </div>

      <!-- Descripción -->
      <div>
        <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">Descripción</label>
        <textarea name="descripcion" rows="3" placeholder="Detalles de la actividad..."
                  style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;resize:vertical;font-family:'DM Sans',sans-serif;"
                  onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'"></textarea>
      </div>

      <!-- Fechas en grid -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">
            Fecha inicio <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="fecha_inicio" id="fecha-inicio" required
                 style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;"
                 onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'"
                 onchange="sincronizarFecha(this.value)">
        </div>
        <div>
          <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">Fecha fin</label>
          <input type="date" name="fecha_fin" id="fecha-fin"
                 style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;"
                 onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'">
        </div>
      </div>

      <!-- Zona y Estado en grid -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">
            Zona <span style="color:#ef4444;">*</span>
          </label>
          <select name="zona" required
                  style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;"
                  onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'">
            <option value="san_martin">San Martín</option>
            <option value="tlaxcala">Tlaxcala</option>
            <option value="ambas" selected>Ambas</option>
          </select>
        </div>
        <div>
          <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">Estado</label>
          <select name="estado"
                  style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;"
                  onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'">
            <option value="planeada" selected>Planeada</option>
            <option value="en_curso">En curso</option>
            <option value="completada">Completada</option>
          </select>
        </div>
      </div>

      <!-- Responsable -->
      <div>
        <label style="display:block;font-size:11.5px;font-weight:600;color:#1a2e2c;margin-bottom:6px;">Responsable</label>
        <select name="id_usuario_responsable"
                style="width:100%;padding:10px 12px;border:1.5px solid #e0f2f0;border-radius:10px;font-size:13px;color:#1a2e2c;background:#fff;outline:none;transition:border .2s;"
                onfocus="this.style.borderColor='#0aafa0'" onblur="this.style.borderColor='#e0f2f0'">
          <option value="">— Sin asignar —</option>
          <?php foreach ($usuarios as $user): ?>
            <option value="<?= (int)$user['id_usuario'] ?>">
              <?= htmlspecialchars($user['nombre'].' '.$user['apellido']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Botones -->
      <div style="display:flex;gap:10px;margin-top:4px;">
        <button type="submit" id="btn-guardar"
                style="flex:1;padding:11px;background:#0aafa0;color:#fff;border:none;border-radius:12px;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          Crear actividad
        </button>
        <button type="button" onclick="cerrarModal()"
                style="padding:11px 20px;background:#f8fffe;border:1.5px solid #e0f2f0;border-radius:12px;font-size:13px;font-weight:600;color:#5a8a84;cursor:pointer;transition:all .2s;">
          Cancelar
        </button>
      </div>

    </form>
  </div>
</div>

<!-- ══ Modal: Detalle del día ══════════════════════════════════════════════ -->
<div id="modal-dia" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center;">
  <div onclick="cerrarModalDia()" style="position:absolute;inset:0;background:rgba(10,30,28,.45);backdrop-filter:blur(4px);"></div>
  <div style="position:relative;z-index:1001;background:#fff;border-radius:20px;width:90%;max-width:440px;max-height:80vh;overflow-y:auto;box-shadow:0 24px 60px rgba(10,175,160,.18);border:1px solid #c8efeb;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #f0fdfa;">
      <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#1a2e2c;" id="modal-dia-titulo">Actividades del día</div>
      <button onclick="cerrarModalDia()" style="width:30px;height:30px;border-radius:8px;border:1px solid #e0f2f0;background:#f8fffe;color:#5a8a84;font-size:18px;cursor:pointer;line-height:1;">×</button>
    </div>
    <div id="modal-dia-contenido" style="padding:18px 22px;"></div>
  </div>
</div>

<script>
// ─── Datos del mes para el modal de día ───────────────────────────────────
const actividadesMes = <?= json_encode($actividades_mes, JSON_UNESCAPED_UNICODE) ?>;
const estadoLabels   = <?= json_encode(array_map(fn($e)=>$e['label'], $estadoMeta)) ?>;
const estadoColors   = <?= json_encode(array_map(fn($e)=>$e['color'], $estadoMeta)) ?>;
const estadoBgs      = <?= json_encode(array_map(fn($e)=>$e['bg'], $estadoMeta)) ?>;
const zonaLabels     = <?= json_encode(array_map(fn($z)=>$z['label'], $zonaMeta)) ?>;
const mes_actual     = <?= (int)$mes_actual ?>;
const anio_actual    = <?= (int)$anio_actual ?>;

// ─── Abrir/cerrar modal de nueva actividad ────────────────────────────────
function abrirModal() {
  const m = document.getElementById('modal-planning');
  m.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function cerrarModal() {
  document.getElementById('modal-planning').style.display = 'none';
  document.body.style.overflow = '';
}

// ─── Sincronizar fecha fin con inicio ────────────────────────────────────
function sincronizarFecha(val) {
  const ff = document.getElementById('fecha-fin');
  if (!ff.value) ff.value = val;
  ff.min = val;
}

// ─── Guardar actividad vía AJAX ───────────────────────────────────────────
function guardarActividad(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-guardar');
  btn.disabled = true;
  btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin .8s linear infinite"><path d="M21 12a9 9 0 1 1-9-9"/></svg> Guardando...';

  const data = new URLSearchParams(new FormData(document.getElementById('form-actividad')));

  fetch('<?= BASE_URL ?>/index.php?pagina=planning&accion=crear', {
    method: 'POST',
    body: data
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      showToast('Actividad creada correctamente');
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(res.error || 'Error al crear la actividad');
      btn.disabled = false;
      btn.innerHTML = '✓ Crear actividad';
    }
  })
  .catch(() => {
    showToast('Error de conexión');
    btn.disabled = false;
    btn.innerHTML = '✓ Crear actividad';
  });
}

// ─── Cambiar estado de actividad ─────────────────────────────────────────
function cambiarEstado(id, estado) {
  fetch('<?= BASE_URL ?>/index.php?pagina=planning&accion=actualizar_estado', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id=' + id + '&estado=' + estado + '&csrf_token=<?= $csrf ?>'
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) showToast('Estado actualizado');
    else showToast(res.error || 'Error al actualizar');
  })
  .catch(() => showToast('Error de conexión'));
}

// ─── Ver detalle del día ──────────────────────────────────────────────────
function verDia(dia) {
  const acts = actividadesMes[dia];
  if (!acts || !acts.length) return;

  const meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  document.getElementById('modal-dia-titulo').textContent = dia + ' de ' + meses[mes_actual] + ' ' + anio_actual;

  let html = '';
  acts.forEach(a => {
    const ec = estadoColors[a.estado] || '#6366f1';
    const eb = estadoBgs[a.estado]   || '#eef2ff';
    const el = estadoLabels[a.estado] || a.estado;
    const zl = zonaLabels[a.zona]    || a.zona;
    const fi = a.fecha_inicio ? a.fecha_inicio.substring(0,10) : '';
    const ff = a.fecha_fin    ? a.fecha_fin.substring(0,10)    : '';

    html += `
      <div style="border:1px solid #e0f2f0;border-radius:12px;padding:14px 16px;margin-bottom:10px;border-left:3px solid ${ec};">
        <div style="font-weight:700;font-size:13.5px;color:#1a2e2c;margin-bottom:8px;">${a.titulo}</div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px;">
          <span style="font-size:10.5px;font-weight:600;color:${ec};background:${eb};padding:2px 9px;border-radius:99px;">${el}</span>
          <span style="font-size:10.5px;font-weight:600;color:#5a8a84;background:#f0fdfa;padding:2px 9px;border-radius:99px;">${zl}</span>
        </div>
        ${a.descripcion ? `<div style="font-size:12px;color:#5a8a84;line-height:1.5;margin-bottom:6px;">${a.descripcion}</div>` : ''}
        ${a.nombre ? `<div style="font-size:11px;color:#5a8a84;">👤 ${a.nombre} ${a.apellido}</div>` : ''}
        <div style="font-size:11px;color:#5a8a84;margin-top:4px;">📅 ${fi}${fi!==ff && ff ? ' → '+ff : ''}</div>
      </div>`;
  });

  document.getElementById('modal-dia-contenido').innerHTML = html;
  document.getElementById('modal-dia').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function cerrarModalDia() {
  document.getElementById('modal-dia').style.display = 'none';
  document.body.style.overflow = '';
}

// ─── Cerrar modales con Escape ────────────────────────────────────────────
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { cerrarModal(); cerrarModalDia(); }
});

// ─── Animación spinner para botón ────────────────────────────────────────
const spinStyle = document.createElement('style');
spinStyle.textContent = '@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
document.head.appendChild(spinStyle);
</script>

<?php require_once 'views/layouts/footer.php'; ?>