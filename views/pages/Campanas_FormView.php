<?php
require_once 'views/layouts/header.php';
$esEdicion = isset($campana);
$errorFormulario = $_SESSION['mensaje'] ?? null;
$tipoErrorFormulario = $_SESSION['tipo_mensaje'] ?? 'error';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>
  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>
    <div class="content" style="padding:24px;">
      <div style="margin-bottom:16px;font-size:13px;color:var(--muted);">
        <a href="<?= BASE_URL ?>/index.php?pagina=campanas" style="color:var(--primary);text-decoration:none;">Campañas</a>
        <span style="margin:0 8px;opacity:.5;">›</span><?= $esEdicion ? 'Editar campaña' : 'Nueva campaña' ?>
      </div>
      <div class="card" style="max-width:760px;">
        <div class="card-header"><h3><?= $esEdicion ? 'Editar campaña' : 'Nueva campaña' ?></h3></div>
        <div class="card-body" style="padding:24px;">
          <?php if ($errorFormulario): ?>
            <div class="alert <?= $tipoErrorFormulario === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><?= e($errorFormulario) ?></div>
          <?php endif; ?>
          <?php $url = BASE_URL . '/index.php?pagina=campanas&accion=guardar' . ($esEdicion ? '&id=' . e((string)$campana['id_campana']) : ''); ?>
          <form method="POST" action="<?= $url ?>" data-validate="true">
            <?= csrfField() ?>
            <div class="form-grid">
              <div class="form-group full">
                <label for="nombre">Nombre <span style="color:#f44336;">*</span></label>
                <input type="text" id="nombre" name="nombre" maxlength="150" required value="<?= e($campana['nombre'] ?? '') ?>" data-rules="required|min:2|max:150">
                <div class="field-error"></div>
              </div>
              <div class="form-group full">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?= e($campana['descripcion'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label for="tipo_meta">Tipo de meta <span style="color:#f44336;">*</span></label>
                <select id="tipo_meta" name="tipo_meta" required data-rules="required">
                  <?php foreach (['economica' => 'Económica', 'especie' => 'En especie', 'mixta' => 'Mixta', 'servicio' => 'Servicio'] as $valor => $etiqueta): ?>
                    <option value="<?= e($valor) ?>" <?= ($campana['tipo_meta'] ?? '') === $valor ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="meta_economica">Meta económica (MXN)</label>
                <input type="number" id="meta_economica" name="meta_economica" min="0" step="0.01" value="<?= e((string)($campana['meta_economica'] ?? '0.00')) ?>" data-rules="min_val:0">
                <div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio <span style="color:#f44336;">*</span></label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required value="<?= e($campana['fecha_inicio'] ?? '') ?>" data-rules="required|date">
                <div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="fecha_cierre">Fecha de cierre <span style="color:#f44336;">*</span></label>
                <input type="date" id="fecha_cierre" name="fecha_cierre" required value="<?= e($campana['fecha_cierre'] ?? '') ?>" data-rules="required|date">
                <div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                  <?php foreach (['borrador', 'activa', 'pausada', 'cerrada', 'cancelada'] as $estado): ?>
                    <option value="<?= e($estado) ?>" <?= ($campana['estado'] ?? 'borrador') === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group full">
                <label for="imagen_url">URL de imagen</label>
                <input type="url" id="imagen_url" name="imagen_url" maxlength="255" value="<?= e($campana['imagen_url'] ?? '') ?>" placeholder="https://...">
              </div>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
              <a href="<?= BASE_URL ?>/index.php?pagina=campanas" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary"><?= $esEdicion ? 'Actualizar campaña' : 'Guardar campaña' ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= BASE_URL ?>/public/js/App.js"></script>
<script src="<?= BASE_URL ?>/public/js/Validaciones.js"></script>
<?php require_once 'views/layouts/footer.php'; ?>
