<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <div style="margin-bottom:16px;font-size:13px;color:var(--muted,#999);">
        <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios" style="color:var(--primary);">Beneficiarios</a>
        <span style="margin:0 8px;">›</span>
        <span><?= $accion === 'editar' ? 'Editar Beneficiario' : 'Nuevo Beneficiario' ?></span>
      </div>

      <div class="card" style="max-width:760px;">
        <div class="card-header">
          <h3>
            <img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="" class="icon-img">
            <?= $accion === 'editar' ? 'Editar Beneficiario' : 'Nuevo Beneficiario' ?>
          </h3>
        </div>

        <div class="card-body" style="padding:24px;">

          <?php if ($accion === 'editar' && !empty($beneficiario)): ?>
            <div style="background:var(--surface,#f5f5f5);padding:14px 16px;border-radius:8px;margin-bottom:24px;font-size:13px;color:var(--muted,#666);">
              <strong style="color:var(--text);">ID:</strong> #<?= e((string)$beneficiario['id_beneficiario']) ?>
              &nbsp;·&nbsp;
              <strong style="color:var(--text);">Creado:</strong>
              <?= isset($beneficiario['created_at']) ? date('d/m/Y H:i', strtotime($beneficiario['created_at'])) : '—' ?>
              <?php if (!empty($beneficiario['updated_at'])): ?>
                &nbsp;·&nbsp;
                <strong style="color:var(--text);">Última actualización:</strong>
                <?= date('d/m/Y H:i', strtotime($beneficiario['updated_at'])) ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <?php
            $formAction = ($accion === 'editar')
              ? BASE_URL . '/index.php?pagina=beneficiarios&accion=editar&id=' . e((string)$beneficiario['id_beneficiario'])
              : BASE_URL . '/index.php?pagina=beneficiarios&accion=crear';
          ?>

          <form method="POST" action="<?= $formAction ?>" id="form-beneficiario" onsubmit="return validarFormBeneficiario()">
            <div class="form-grid">

              <div class="form-group full">
                <label for="nombre_completo">Nombre completo <span style="color:#f44336;">*</span></label>
                <input type="text" id="nombre_completo" name="nombre_completo"
                       value="<?= e($beneficiario['nombre_completo'] ?? '') ?>"
                       placeholder="Ej: Ana Lucía Mendoza Ríos"
                       maxlength="200" required autocomplete="name">
              </div>

              <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad"
                       value="<?= e((string)($beneficiario['edad'] ?? '')) ?>"
                       placeholder="Ej: 34" min="0" max="150">
              </div>

              <div class="form-group">
                <label for="id_comunidad">Comunidad <span style="color:#f44336;">*</span></label>
                <select id="id_comunidad" name="id_comunidad" required>
                  <option value="">— Selecciona una comunidad —</option>
                  <?php foreach ($comunidades as $comunidad): ?>
                    <option value="<?= e((string)$comunidad['id_comunidad']) ?>"
                      <?= (!empty($beneficiario['id_comunidad']) && (int)$beneficiario['id_comunidad'] === (int)$comunidad['id_comunidad']) ? 'selected' : '' ?>>
                      <?= e($comunidad['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group full">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion"
                       value="<?= e($beneficiario['direccion'] ?? '') ?>"
                       placeholder="Ej: Calle Hidalgo 123"
                       maxlength="255" autocomplete="street-address">
              </div>

              <div class="form-group full">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono"
                       value="<?= e($beneficiario['telefono'] ?? '') ?>"
                       placeholder="Ej: 222-300-1001" maxlength="20">
              </div>

              <div class="form-group">
                <label for="estado">Estado <span style="color:#f44336;">*</span></label>
                <select id="estado" name="estado" required>
                  <?php
                    $estados = ['activo' => 'Activo', 'en_espera' => 'En espera', 'inactivo' => 'Inactivo'];
                  ?>
                  <?php foreach ($estados as $codigo => $texto): ?>
                    <option value="<?= e($codigo) ?>"
                      <?= (!empty($beneficiario['estado']) && $beneficiario['estado'] === $codigo) ? 'selected' : '' ?>>
                      <?= e($texto) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group full">
                <label for="notas">Notas</label>
                <textarea id="notas" name="notas" rows="5" placeholder="Describe la situación del beneficiario..." maxlength="1000"><?= e($beneficiario['notas'] ?? '') ?></textarea>
              </div>

            </div>

            <div id="form-error" style="display:none;background:#fdecea;color:#c62828;padding:10px 14px;border-radius:6px;border-left:4px solid #f44336;margin-top:12px;font-size:13px;"></div>

            <div style="display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border,#eee);">
              <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios" class="btn btn-secondary">← Volver a beneficiarios</a>
              <button type="submit" class="btn btn-primary">
                <img src="<?= BASE_URL ?>/public/iconos/icons8-guardar-50.png" alt="" class="icon-img" style="width:16px;height:16px;">
                <?= $accion === 'editar' ? 'Guardar cambios' : 'Registrar beneficiario' ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function validarFormBeneficiario() {
  var nombre = document.getElementById('nombre_completo');
  var comunidad = document.getElementById('id_comunidad');
  var estado = document.getElementById('estado');
  var errorDiv = document.getElementById('form-error');

  errorDiv.style.display = 'none';
  errorDiv.textContent = '';

  if (!nombre.value.trim()) {
    errorDiv.textContent = 'El nombre completo es obligatorio.';
    errorDiv.style.display = 'block';
    nombre.focus();
    return false;
  }

  if (!comunidad.value) {
    errorDiv.textContent = 'Selecciona una comunidad válida.';
    errorDiv.style.display = 'block';
    comunidad.focus();
    return false;
  }

  if (!estado.value) {
    errorDiv.textContent = 'Selecciona el estado del beneficiario.';
    errorDiv.style.display = 'block';
    estado.focus();
    return false;
  }

  return true;
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
