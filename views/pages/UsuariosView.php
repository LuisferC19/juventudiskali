<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- ===== Tarjetas de estadísticas ===== -->
      <div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:var(--primary);"><?= $total ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Usuarios totales</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#4caf50;"><?= $total_activos ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Usuarios activos</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#f44336;"><?= $total - $total_activos ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Usuarios inactivos</div>
        </div>

      </div>

      <!-- ===== Tabla principal ===== -->
      <div class="card">
        <div class="card-header">
          <h3>
            <img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="Usuarios" class="icon-img">
            Gestión de Usuarios
          </h3>
          <div class="toolbar">
            <input
              type="text"
              class="search-input"
              id="buscar-usuarios"
              placeholder="Buscar por nombre, email o rol…"
              oninput="filtrarTabla(this,'tabla-usuarios')"
            >
            <button class="btn btn-primary btn-icon" onclick="openModal('modal-crear-usuario')">
              <img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="Nuevo" class="icon-img">
              Nuevo Usuario
            </button>
          </div>
        </div>

        <!-- ── Mensaje de retroalimentación ── -->
        <?php if (!empty($mensaje)): ?>
          <?php
            $bg     = $tipo_mensaje === 'error'   ? '#fdecea' : ($tipo_mensaje === 'warning' ? '#fff8e1' : '#e8f5e9');
            $color  = $tipo_mensaje === 'error'   ? '#c62828' : ($tipo_mensaje === 'warning' ? '#e65100' : '#2e7d32');
            $border = $tipo_mensaje === 'error'   ? '#f44336' : ($tipo_mensaje === 'warning' ? '#ffa000' : '#4caf50');
            $icon   = $tipo_mensaje === 'error'   ? '⚠️'      : ($tipo_mensaje === 'warning' ? '⚠️'      : '✅');
          ?>
          <div role="alert"
               style="margin:16px 16px 0;padding:12px 16px;border-radius:6px;
                      background:<?= $bg ?>;color:<?= $color ?>;
                      border-left:4px solid <?= $border ?>;">
            <?= $icon ?>
            <?= $mensaje /* puede contener <strong> */ ?>
          </div>
        <?php endif; ?>

        <!-- ── Tabla de usuarios ── -->
        <div style="overflow-x:auto;margin-top:16px;">
          <table id="tabla-usuarios" style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="background:var(--surface,#f5f5f5);border-bottom:2px solid var(--border,#ddd);">
                <th style="padding:12px 16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">ID</th>
                <th style="padding:12px 16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Nombre Completo</th>
                <th style="padding:12px 16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Correo Electrónico</th>
                <th style="padding:12px 16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Rol</th>
                <th style="padding:12px 16px;text-align:center;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Estado</th>
                <th style="padding:12px 16px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Creado</th>
                <th style="padding:12px 16px;text-align:center;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($usuarios)): ?>
                <tr>
                  <td colspan="7" style="padding:40px;text-align:center;color:var(--muted,#999);">
                    No hay usuarios registrados.
                    <a href="#" onclick="openModal('modal-crear-usuario');return false;" style="color:var(--primary);">
                      Crear el primero →
                    </a>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                  <tr style="border-bottom:1px solid var(--border,#eee);" onmouseover="this.style.background='var(--surface,#fafafa)'" onmouseout="this.style.background=''">

                    <td style="padding:12px 16px;color:var(--muted,#999);font-size:13px;">#<?= e((string)$u['id_usuario']) ?></td>

                    <td style="padding:12px 16px;">
                      <div style="display:flex;align-items:center;gap:10px;">
                        <!-- Avatar con iniciales -->
                        <div style="width:34px;height:34px;border-radius:50%;background:var(--primary,#3f51b5);color:#fff;
                                    display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;flex-shrink:0;">
                          <?= strtoupper(substr($u['nombre'], 0, 1) . substr($u['apellido'], 0, 1)) ?>
                        </div>
                        <div>
                          <strong><?= e($u['nombre']) ?> <?= e($u['apellido']) ?></strong>
                          <?php if ((int)$u['id_usuario'] === (int)$_SESSION['id_usuario']): ?>
                            <span style="background:#e3f2fd;color:#1976d2;font-size:10px;padding:1px 6px;border-radius:3px;margin-left:4px;">Tú</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>

                    <td style="padding:12px 16px;font-size:13px;"><?= e($u['email']) ?></td>

                    <td style="padding:12px 16px;">
                      <span style="background:#e8eaf6;color:#3949ab;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:500;">
                        <?= e($u['rol'] ?: 'Sin rol') ?>
                      </span>
                    </td>

                    <td style="padding:12px 16px;text-align:center;">
                      <?php if ($u['activo']): ?>
                        <span style="background:#e8f5e9;color:#2e7d32;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:500;">
                          ● Activo
                        </span>
                      <?php else: ?>
                        <span style="background:#fdecea;color:#c62828;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:500;">
                          ● Inactivo
                        </span>
                      <?php endif; ?>

                      <!-- Indicador de cuenta bloqueada -->
                      <?php if ((int)$u['intentos_fallidos'] >= 5): ?>
                        <br>
                        <span style="background:#fff3e0;color:#e65100;padding:2px 8px;border-radius:12px;font-size:11px;margin-top:3px;display:inline-block;">
                          🔒 Bloqueado
                        </span>
                      <?php endif; ?>
                    </td>

                    <td style="padding:12px 16px;font-size:12px;color:var(--muted,#999);">
                      <?= isset($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?>
                    </td>

                    <td style="padding:12px 16px;text-align:center;white-space:nowrap;">
                      <!-- Botón Editar -->
                      <a href="<?= BASE_URL ?>/index.php?pagina=usuarios&accion=editar&id=<?= e((string)$u['id_usuario']) ?>"
                         class="btn btn-secondary btn-sm"
                         title="Editar usuario"
                         style="margin:2px;">
                        ✎ Editar
                      </a>

                      <?php if ((int)$u['id_usuario'] !== (int)$_SESSION['id_usuario']): ?>
                        <!-- Botón Eliminar (solo si no es el usuario actual) -->
                        <a href="<?= BASE_URL ?>/index.php?pagina=usuarios&accion=borrar&id=<?= e((string)$u['id_usuario']) ?>"
                           class="btn btn-danger btn-sm"
                           title="Eliminar usuario"
                           style="margin:2px;"
                           onclick="return confirm('¿Eliminar a <?= e($u['nombre']) ?> <?= e($u['apellido']) ?>?\nEsta acción no se puede deshacer.');">
                          ✕ Borrar
                        </a>
                      <?php endif; ?>

                      <?php if ((int)$u['intentos_fallidos'] >= 5): ?>
                        <!-- Botón Desbloquear (solo si está bloqueado) -->
                        <a href="<?= BASE_URL ?>/index.php?pagina=usuarios&accion=desbloquear&id=<?= e((string)$u['id_usuario']) ?>"
                           class="btn btn-sm"
                           title="Desbloquear cuenta"
                           style="margin:2px;background:#fff3e0;color:#e65100;border:1px solid #ffb74d;"
                           onclick="return confirm('¿Desbloquear la cuenta de <?= e($u['nombre']) ?>?');">
                          🔓 Desbloquear
                        </a>
                      <?php endif; ?>
                    </td>

                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- ── Pie de tabla ── -->
        <div style="margin:16px;padding-top:12px;border-top:1px solid var(--border,#eee);
                    color:var(--muted,#999);font-size:12px;display:flex;justify-content:space-between;align-items:center;">
          <span><strong><?= $total ?></strong> usuario(s) registrado(s)</span>
          <span style="font-size:11px;">Última actualización: <?= date('d/m/Y H:i') ?></span>
        </div>

      </div><!-- /card tabla -->

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<!-- ============================================================
     MODAL: Crear Nuevo Usuario
     Formulario POST → ?pagina=usuarios&accion=crear
     ============================================================ -->
<div class="modal-overlay" id="modal-crear-usuario">
  <div class="modal" style="max-width:560px;width:95%;">

    <div class="modal-header">
      <h3>
        <img src="<?= BASE_URL ?>/public/iconos/icons8-añadir-50.png" alt="" class="icon-img" style="width:24px;height:24px;">
        Crear Nuevo Usuario
      </h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-crear-usuario')">✕</button>
    </div>

    <form method="POST"
          action="<?= BASE_URL ?>/index.php?pagina=usuarios&accion=crear"
          id="form-crear-usuario"
          onsubmit="return validarFormCrear()">

      <div class="modal-body">
        <div class="form-grid">

          <!-- Nombre -->
          <div class="form-group">
            <label for="c_nombre">Nombre <span style="color:#f44336;">*</span></label>
            <input type="text" id="c_nombre" name="nombre"
                   placeholder="Ej: Juan" maxlength="100" required
                   autocomplete="given-name">
          </div>

          <!-- Apellido -->
          <div class="form-group">
            <label for="c_apellido">Apellido <span style="color:#f44336;">*</span></label>
            <input type="text" id="c_apellido" name="apellido"
                   placeholder="Ej: García López" maxlength="100" required
                   autocomplete="family-name">
          </div>

          <!-- Email -->
          <div class="form-group full">
            <label for="c_email">Correo Electrónico <span style="color:#f44336;">*</span></label>
            <input type="email" id="c_email" name="email"
                   placeholder="correo@iskalli.mx" maxlength="150" required
                   autocomplete="email">
          </div>

          <!-- Contraseña -->
          <div class="form-group">
            <label for="c_password">Contraseña <span style="color:#f44336;">*</span></label>
            <input type="password" id="c_password" name="password"
                   placeholder="Mínimo 6 caracteres" minlength="6" required
                   autocomplete="new-password">
          </div>

          <!-- Confirmar contraseña -->
          <div class="form-group">
            <label for="c_confirmar">Confirmar Contraseña <span style="color:#f44336;">*</span></label>
            <input type="password" id="c_confirmar" name="confirmar_password"
                   placeholder="Repite la contraseña" minlength="6" required
                   autocomplete="new-password">
          </div>

          <!-- Rol -->
          <div class="form-group full">
            <label for="c_rol">Rol del Usuario <span style="color:#f44336;">*</span></label>
            <select id="c_rol" name="id_rol" required>
              <option value="">— Selecciona un rol —</option>
              <?php foreach ($roles as $rol): ?>
                <option value="<?= e((string)$rol['id_rol']) ?>">
                  <?= e($rol['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Activo -->
          <div class="form-group full form-checkbox">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
              <input type="checkbox" name="activo" value="1" checked>
              <span>Usuario activo al crear</span>
            </label>
          </div>

        </div><!-- /form-grid -->

        <!-- Mensaje de error del modal (validación JS) -->
        <div id="modal-error-crear"
             style="display:none;background:#fdecea;color:#c62828;padding:10px 14px;
                    border-radius:6px;border-left:4px solid #f44336;margin-top:12px;font-size:13px;">
        </div>

      </div><!-- /modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-crear-usuario')">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
          <img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="" class="icon-img" style="width:16px;height:16px;">
          Guardar Usuario
        </button>
      </div>

    </form>
  </div><!-- /modal -->
</div><!-- /modal-overlay -->

<!-- ── Script de validación del modal ── -->
<script>
function validarFormCrear() {
  var errorDiv = document.getElementById('modal-error-crear');
  var pass     = document.getElementById('c_password').value;
  var confirm  = document.getElementById('c_confirmar').value;

  errorDiv.style.display = 'none';
  errorDiv.textContent   = '';

  if (pass.length < 6) {
    errorDiv.style.display = 'block';
    errorDiv.textContent   = 'La contraseña debe tener al menos 6 caracteres.';
    document.getElementById('c_password').focus();
    return false;
  }

  if (pass !== confirm) {
    errorDiv.style.display = 'block';
    errorDiv.textContent   = 'Las contraseñas no coinciden. Verifica e intenta de nuevo.';
    document.getElementById('c_confirmar').focus();
    return false;
  }

  return true;
}

// Limpiar el formulario de crear cuando se cierre el modal
document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.getElementById('modal-crear-usuario');
  if (overlay) {
    overlay.addEventListener('click', function (e) {
      // clic en el overlay (fuera del modal) cierra
      if (e.target === overlay) closeModal('modal-crear-usuario');
    });
  }

  // Auto-abrir modal si hubo error de validación (recarga con mensaje de error)
  <?php if (!empty($mensaje) && ($tipo_mensaje ?? '') === 'error'): ?>
  // openModal('modal-crear-usuario');
  <?php endif; ?>
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>