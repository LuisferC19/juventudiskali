<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Total actividades</div>
          <div class="kpi-value"><?= $kpis['total_actividades'] ?></div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Esta semana</div>
          <div class="kpi-value"><?= $kpis['actividades_semana'] ?></div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">San Martín</div>
          <div class="kpi-value"><?= $kpis['san_martin'] ?></div>
        </div>
        <div class="kpi pink">
          <div class="kpi-label">Tlaxcala</div>
          <div class="kpi-value"><?= $kpis['tlaxcala'] ?></div>
        </div>
      </div>

      <!-- Filtro por zona y botón para crear -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:10px;">
        <div>
          <label style="font-size:12px;color:var(--muted);margin-right:8px;">Filtrar por zona:</label>
          <select id="filtro-zona" onchange="filtrarActividades()" style="padding:6px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
            <option value="todas">Todas</option>
            <option value="san_martin">San Martín</option>
            <option value="tlaxcala">Tlaxcala</option>
            <option value="ambas">Ambas</option>
          </select>
        </div>
        <button class="btn btn-primary" onclick="openModalNuevaActividad()">+ Nueva actividad</button>
      </div>

      <!-- Calendario mensual -->
      <div class="card">
        <div class="card-header">
          <h3>Calendario de actividades — <?= strftime('%B %Y', mktime(0, 0, 0, $mes_actual, 1, $anio_actual)) ?></h3>
          <div style="display:flex;gap:10px;">
            <button class="btn btn-secondary btn-sm" onclick="cambiarMes(-1)">← Mes anterior</button>
            <button class="btn btn-secondary btn-sm" onclick="cambiarMes(0)">Hoy</button>
            <button class="btn btn-secondary btn-sm" onclick="cambiarMes(1)">Mes siguiente →</button>
          </div>
        </div>

        <div class="calendar-grid" id="calendario">
          <!-- Encabezados de días -->
          <div class="calendar-header">Lun</div>
          <div class="calendar-header">Mar</div>
          <div class="calendar-header">Mié</div>
          <div class="calendar-header">Jue</div>
          <div class="calendar-header">Vie</div>
          <div class="calendar-header">Sáb</div>
          <div class="calendar-header">Dom</div>

          <!-- Días del mes -->
          <?php
            $primer_dia = mktime(0, 0, 0, $mes_actual, 1, $anio_actual);
            $ultimo_dia = mktime(0, 0, 0, $mes_actual + 1, 0, $anio_actual);
            $num_dias = date('d', $ultimo_dia);
            $dia_semana_inicio = date('N', $primer_dia); // 1=lunes, 7=domingo

            // Llenar con días vacíos del mes anterior
            for ($i = 1; $i < $dia_semana_inicio; $i++) {
                echo '<div class="calendar-day calendar-empty"></div>';
            }

            // Días del mes actual
            for ($dia = 1; $dia <= $num_dias; $dia++) {
                $tiene_actividades = isset($actividades_mes[$dia]);
                $clase = $tiene_actividades ? 'calendar-day-with-activity' : 'calendar-day';
                echo "<div class=\"$clase\" onclick=\"abrirDetalleActividad($dia)\">";
                echo "<div class=\"calendar-day-num\">$dia</div>";
                
                if ($tiene_actividades) {
                    foreach ($actividades_mes[$dia] as $act) {
                        $badge_clase = 'badge-' . $act['estado'];
                        echo "<div class=\"calendar-activity\" title=\"{$act['titulo']}\"><span class=\"badge badge-sm $badge_clase\">" . substr($act['titulo'], 0, 12) . "...</span></div>";
                    }
                }
                
                echo "</div>";
            }

            // Llenar con días vacíos del mes siguiente
            $dias_finales = (7 - (($num_dias + $dia_semana_inicio - 1) % 7)) % 7;
            for ($i = 0; $i < $dias_finales; $i++) {
                echo '<div class="calendar-day calendar-empty"></div>';
            }
          ?>
        </div>
      </div>

      <!-- Tabla de actividades -->
      <div class="card" style="margin-top:20px;">
        <div class="card-header">
          <h3>Listado de actividades</h3>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>Fecha inicio</th>
              <th>Fecha fin</th>
              <th>Zona</th>
              <th>Estado</th>
              <th>Responsable</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($actividades as $act): ?>
              <tr>
                <td>#<?= (int)$act['id_actividad'] ?></td>
                <td><strong><?= htmlspecialchars($act['titulo']) ?></strong></td>
                <td><?= date('d/m/Y', strtotime($act['fecha_inicio'])) ?></td>
                <td><?= date('d/m/Y', strtotime($act['fecha_fin'])) ?></td>
                <td>
                  <span style="font-size:11px;background:var(--border);padding:3px 8px;border-radius:4px;text-transform:capitalize;">
                    <?= htmlspecialchars($act['zona']) ?>
                  </span>
                </td>
                <td>
                  <?php
                    $estado_clase = match($act['estado']) {
                      'planeada' => 'badge-gray',
                      'en_curso' => 'badge-amber',
                      'completada' => 'badge-green',
                      'cancelada' => 'badge-danger',
                      default => 'badge-blue',
                    };
                  ?>
                  <select class="badge <?= $estado_clase ?>" onchange="cambiarEstadoActividad(<?= (int)$act['id_actividad'] ?>, this.value)" style="padding:4px 6px;font-size:11px;cursor:pointer;">
                    <option value="planeada" <?= $act['estado'] === 'planeada' ? 'selected' : '' ?>>Planeada</option>
                    <option value="en_curso" <?= $act['estado'] === 'en_curso' ? 'selected' : '' ?>>En curso</option>
                    <option value="completada" <?= $act['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                    <option value="cancelada" <?= $act['estado'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                  </select>
                </td>
                <td>
                  <?php if (!empty($act['nombre'])): ?>
                    <?= htmlspecialchars($act['nombre'] . ' ' . $act['apellido']) ?>
                  <?php else: ?>
                    <span style="color:var(--muted);font-size:11px;">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn btn-xs btn-secondary" onclick="editarActividad(<?= (int)$act['id_actividad'] ?>)">Editar</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php if (empty($actividades)): ?>
          <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">No hay actividades registradas</div>
        <?php endif; ?>
      </div>

      <!-- Modal para crear nueva actividad -->
      <div id="modal-nueva-actividad" class="modal" style="display:none;">
        <div class="modal-overlay" onclick="closeModal('modal-nueva-actividad')"></div>
        <div class="modal-content" style="width:90%;max-width:500px;">
          <div class="modal-header">
            <h3>Nueva Actividad</h3>
            <button onclick="closeModal('modal-nueva-actividad')" class="modal-close">×</button>
          </div>
          <div class="modal-body" style="padding:20px;">
            <form id="form-nueva-actividad" onsubmit="crearActividad(event)">
              <div class="form-group">
                <label for="titulo-act">Título <span style="color:#f44336;">*</span></label>
                <input type="text" id="titulo-act" name="titulo" required maxlength="150" placeholder="Ej: Reforestación en parque central" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;">
              </div>

              <div class="form-group">
                <label for="desc-act">Descripción</label>
                <textarea id="desc-act" name="descripcion" rows="3" placeholder="Detalles de la actividad..." style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;resize:vertical;"></textarea>
              </div>

              <div class="form-group">
                <label for="fecha-inicio-act">Fecha de inicio <span style="color:#f44336;">*</span></label>
                <input type="date" id="fecha-inicio-act" name="fecha_inicio" required style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;">
              </div>

              <div class="form-group">
                <label for="fecha-fin-act">Fecha de fin</label>
                <input type="date" id="fecha-fin-act" name="fecha_fin" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;">
              </div>

              <div class="form-group">
                <label for="zona-act">Zona <span style="color:#f44336;">*</span></label>
                <select id="zona-act" name="zona" required style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;">
                  <option value="san_martin">San Martín</option>
                  <option value="tlaxcala">Tlaxcala</option>
                  <option value="ambas" selected>Ambas</option>
                </select>
              </div>

              <div class="form-group">
                <label for="responsable-act">Responsable</label>
                <select id="responsable-act" name="id_usuario_responsable" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;">
                  <option value="">— Sin asignar —</option>
                  <?php foreach ($usuarios as $user): ?>
                    <option value="<?= (int)$user['id_usuario'] ?>">
                      <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="estado-act">Estado</label>
                <select id="estado-act" name="estado" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;">
                  <option value="planeada" selected>Planeada</option>
                  <option value="en_curso">En curso</option>
                  <option value="completada">Completada</option>
                </select>
              </div>

              <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Crear actividad</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-nueva-actividad')" style="flex:1;">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: var(--border);
  padding: 1px;
  border-radius: 8px;
  overflow: hidden;
}

.calendar-header {
  background: var(--teal);
  color: #fff;
  padding: 12px;
  text-align: center;
  font-weight: 700;
  font-size: 12px;
}

.calendar-day {
  background: #fff;
  min-height: 100px;
  padding: 8px;
  position: relative;
  cursor: pointer;
  transition: background 0.2s;
}

.calendar-day:hover {
  background: rgba(10, 175, 160, 0.05);
}

.calendar-empty {
  background: var(--fondo);
  cursor: default;
}

.calendar-empty:hover {
  background: var(--fondo);
}

.calendar-day-with-activity {
  background: rgba(10, 175, 160, 0.08);
  min-height: 100px;
  padding: 8px;
  position: relative;
  cursor: pointer;
  border-left: 3px solid var(--teal);
}

.calendar-day-num {
  font-weight: 700;
  font-size: 13px;
  margin-bottom: 4px;
}

.calendar-activity {
  margin: 3px 0;
  font-size: 10px;
  display: flex;
  gap: 2px;
  flex-wrap: wrap;
}

.badge-sm {
  font-size: 9px;
  padding: 2px 4px;
}

.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  align-items: center;
  justify-content: center;
}

.modal-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  cursor: pointer;
}

.modal-content {
  position: relative;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-height: 90vh;
  overflow-y: auto;
  z-index: 1001;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
}

.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: var(--muted);
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 6px;
  color: var(--texto);
}
</style>

<script>
function openModalNuevaActividad() {
  document.getElementById('modal-nueva-actividad').style.display = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

function crearActividad(e) {
  e.preventDefault();
  const form = document.getElementById('form-nueva-actividad');
  const formData = new FormData(form);

  fetch('<?= BASE_URL ?>/index.php?pagina=planning&accion=crear', {
    method: 'POST',
    body: new URLSearchParams(formData)
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast('Actividad creada correctamente', 'success');
      setTimeout(() => location.reload(), 500);
    } else {
      showToast(data.error || 'Error al crear la actividad', 'error');
    }
  })
  .catch(e => showToast('Error: ' + e.message, 'error'));
}

function cambiarEstadoActividad(id, estado) {
  fetch('<?= BASE_URL ?>/index.php?pagina=planning&accion=actualizar_estado', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + id + '&estado=' + estado
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast('Estado actualizado', 'success');
      setTimeout(() => location.reload(), 500);
    }
  })
  .catch(e => console.error('Error:', e));
}

function filtrarActividades() {
  showToast('Filtro aplicado');
}

function cambiarMes(direccion) {
  showToast('Cambio de mes: ' + (direccion > 0 ? 'siguiente' : direccion < 0 ? 'anterior' : 'actual'));
}

function abrirDetalleActividad(dia) {
  showToast('Actividades del día ' + dia);
}

function editarActividad(id) {
  showToast('Editar actividad #' + id);
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
