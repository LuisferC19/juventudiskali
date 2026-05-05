<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- Breadcrumb -->
      <div style="margin-bottom:16px;font-size:13px;color:var(--muted,#999);">
        <a href="<?= BASE_URL ?>/index.php?pagina=usuarios" style="color:var(--primary);">Usuarios</a>
        <span style="margin:0 8px;">›</span>
        <span><?= $accion === 'editar' ? 'Editar Usuario' : 'Nuevo Usuario' ?></span>
      </div>

      <div class="card" style="max-width:640px;">

        <div class="card-header">
          <h3>
            <img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="" class="icon-img">
            <?= $accion === 'editar' ? 'Editar Usuario' : 'Nuevo Usuario' ?>
          </h3>
        </div>

        <div class="card-body" style="padding:24px;">

          <?php if ($accion === 'editar' && !empty($usuario)): ?>
            <!-- Información del usuario actual -->
            <div style="background:var(--surface,#f5f5f5);padding:14px 16px;border-radius:8px;
                        margin-bottom:24px;font-size:13px;color:var(--muted,#666);">
              <strong style="color:var(--text);">ID:</strong> #<?= e((string)$usuario['id_usuario']) ?>
              &nbsp;·&nbsp;
              <strong style="color:var(--text);">Creado:</strong>
              <?= isset($usuario['created_at']) ? date('d/m/Y H:i', strtotime($usuario['created_at'])) : '—' ?>
              <?php if (!empty($usuario['ultimo_acceso'])): ?>
                &nbsp;·&nbsp;
                <strong style="color:var(--text);">Último acceso:</strong>
                <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- ============================================================
               FORMULARIO
               Si accion=editar  → POST ?pagina=usuarios&accion=editar&id=N
               Si accion=nuevo   → POST ?pagina=usuarios&accion=crear
               ============================================================ -->
          <?php
            $formAction = ($accion === 'editar')
              ? BASE_URL . '/index.php?pagina=usuarios&accion=editar&id=' . e((string)($usuario['id_usuario'] ?? 0))
              : BASE_URL . '/index.php?pagina=usuarios&accion=crear';
          ?>
          <form method="POST"
                action="<?= $formAction ?>"
                id="form-usuario"
                onsubmit="return validarFormUsuario()">

            <div class="form-grid">

              <!-- Nombre -->
              <div class="form-group">
                <label for="nombre">Nombre <span style="color:#f44336;">*</span></label>
                <input type="text" id="nombre" name="nombre"
                       value="<?= e($usuario['nombre'] ?? '') ?>"
                       placeholder="Ej: Juan" maxlength="100" required
                       autocomplete="given-name">
              </div>

              <!-- Apellido -->
              <div class="form-group">
                <label for="apellido">Apellido <span style="color:#f44336;">*</span></label>
                <input type="text" id="apellido" name="apellido"
                       value="<?= e($usuario['apellido'] ?? '') ?>"
                       placeholder="Ej: García López" maxlength="100" required
                       autocomplete="family-name">
              </div>

              <!-- Email -->
              <div class="form-group full">
                <label for="email">Correo Electrónico <span style="color:#f44336;">*</span></label>
                <input type="email" id="email" name="email"
                       value="<?= e($usuario['email'] ?? '') ?>"
                       placeholder="correo@iskalli.mx" maxlength="150" required
                       autocomplete="email">
              </div>

              <!-- Contraseña -->
              <div class="form-group">
                <label for="password">
                  <?= $accion === 'editar' ? 'Nueva Contraseña (opcional)' : 'Contraseña *' ?>
                </label>
                <input type="password" id="password" name="password"
                       placeholder="<?= $accion === 'editar' ? 'Dejar vacío para mantener la actual' : 'Mínimo 6 caracteres' ?>"
                       minlength="6"
                       <?= $accion !== 'editar' ? 'required' : '' ?>
                       autocomplete="new-password">
                <?php if ($accion === 'editar'): ?>
                  <small style="color:var(--muted,#999);font-size:11px;margin-top:4px;display:block;">
                    Solo ingresa si deseas cambiar la contraseña actual.
                  </small>
                <?php endif; ?>
              </div>

              <!-- Confirmar contraseña -->
              <div class="form-group" id="campo-confirmar"
                   style="<?= $accion === 'editar' ? 'display:none;' : '' ?>">
                <label for="confirmar_password">
                  Confirmar Contraseña <span style="color:#f44336;" id="asterisco-confirmar">*</span>
                </label>
                <input type="password" id="confirmar_password" name="confirmar_password"
                       placeholder="Repite la contraseña"
                       minlength="6"
                       <?= $accion !== 'editar' ? 'required' : '' ?>
                       autocomplete="new-password">
              </div>

              <!-- Rol -->
              <div class="form-group full">
                <label for="id_rol">Rol del Usuario <span style="color:#f44336;">*</span></label>
                <select id="id_rol" name="id_rol" required>
                  <option value="">— Selecciona un rol —</option>
                  <?php foreach ($roles as $rol): ?>
                    <option value="<?= e((string)$rol['id_rol']) ?>"
                      <?= (!empty($usuario['id_rol']) && (int)$rol['id_rol'] === (int)$usuario['id_rol']) ? 'selected' : '' ?>>
                      <?= e($rol['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Estado activo -->
              <div class="form-group full form-checkbox">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                  <input type="checkbox" name="activo" value="1"
                         <?= (!isset($usuario['activo']) || $usuario['activo']) ? 'checked' : '' ?>>
                  <span>Usuario activo</span>
                </label>
              </div>

            </div><!-- /form-grid -->

            <!-- Mensaje de error validación JS -->
            <div id="form-error"
                 style="display:none;background:#fdecea;color:#c62828;padding:10px 14px;
                        border-radius:6px;border-left:4px solid #f44336;margin-top:12px;font-size:13px;">
            </div>

            <!-- Botones de acción -->
            <div style="display:flex;gap:12px;margin-top:24px;padding-top:20px;
                        border-top:1px solid var(--border,#eee);">
              <a href="<?= BASE_URL ?>/index.php?pagina=usuarios"
                 class="btn btn-secondary">
                ← Volver a la Lista
              </a>
              <button type="submit" class="btn btn-primary">
                <img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="" class="icon-img" style="width:16px;height:16px;">
                <?= $accion === 'editar' ? 'Guardar Cambios' : 'Crear Usuario' ?>
              </button>
            </div>

          </form>
        </div><!-- /card-body -->
      </div><!-- /card -->

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var passwordInput  = document.getElementById('password');
  var confirmarCampo = document.getElementById('campo-confirmar');
  var confirmarInput = document.getElementById('confirmar_password');
  var esEdicion      = <?= $accion === 'editar' ? 'true' : 'false' ?>;

  if (esEdicion) {
    // En edición: mostrar/ocultar campo "confirmar" según si hay texto en password
    passwordInput.addEventListener('input', function () {
      if (this.value.length > 0) {
        confirmarCampo.style.display = 'block';
        confirmarInput.required      = true;
      } else {
        confirmarCampo.style.display = 'none';
        confirmarInput.required      = false;
        confirmarInput.value         = '';
      }
    });
  }
});

function validarFormUsuario() {
  var errorDiv      = document.getElementById('form-error');
  var passwordInput = document.getElementById('password');
  var confirmarInput= document.getElementById('confirmar_password');
  var esEdicion     = <?= $accion === 'editar' ? 'true' : 'false' ?>;

  errorDiv.style.display = 'none';
  errorDiv.textContent   = '';

  // Si hay contraseña ingresada (requerido en crear, opcional en editar)
  if (!esEdicion || passwordInput.value.length > 0) {
    if (passwordInput.value.length < 6) {
      errorDiv.style.display = 'block';
      errorDiv.textContent   = 'La contraseña debe tener al menos 6 caracteres.';
      passwordInput.focus();
      return false;
    }

    if (passwordInput.value !== confirmarInput.value) {
      errorDiv.style.display = 'block';
      errorDiv.textContent   = 'Las contraseñas no coinciden. Verifica e intenta de nuevo.';
      confirmarInput.focus();
      return false;
    }
  }

  return true;
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>