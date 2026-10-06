<?php
require_once 'views/layouts/header.php';
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
        <a href="<?= BASE_URL ?>/index.php?pagina=entregas" style="color:var(--primary);text-decoration:none;">Entregas</a>
        <span style="margin:0 8px;opacity:.5;">›</span>Nueva entrega
      </div>
      <div class="card" style="max-width:800px;">
        <div class="card-header"><h3>Nueva entrega</h3></div>
        <div class="card-body" style="padding:24px;">
          <?php if ($errorFormulario): ?><div class="alert <?= $tipoErrorFormulario === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><?= e($errorFormulario) ?></div><?php endif; ?>
          <?php if (!$donaciones): ?><div class="alert alert-danger" role="alert">No hay donaciones verificadas disponibles para entregar.</div><?php endif; ?>
          <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=entregas&accion=guardar" data-validate="true">
            <?= csrfField() ?>
            <div class="form-grid">
              <div class="form-group full">
                <label for="id_donacion">Donación verificada <span style="color:#f44336;">*</span></label>
                <select id="id_donacion" name="id_donacion" required data-rules="required">
                  <option value="">Selecciona una donación verificada</option>
                  <?php foreach ($donaciones as $donacion): ?>
                    <option value="<?= e((string)$donacion['id_donacion']) ?>">#<?= e((string)$donacion['id_donacion']) ?> · <?= e($donacion['tipo_donacion']) ?> · <?= e($donacion['donador']) ?> · <?= e($donacion['campana']) ?> (<?= e((string)$donacion['cantidad']) ?> <?= e($donacion['unidad_medida'] ?? '') ?>)</option>
                  <?php endforeach; ?>
                </select><div class="field-error"></div>
              </div>
              <div class="form-group full">
                <label for="id_beneficiario">Beneficiario <span style="color:#f44336;">*</span></label>
                <select id="id_beneficiario" name="id_beneficiario" required data-rules="required">
                  <option value="">Selecciona un beneficiario</option>
                  <?php foreach ($beneficiarios as $beneficiario): ?><option value="<?= e((string)$beneficiario['id_beneficiario']) ?>"><?= e($beneficiario['nombre']) ?></option><?php endforeach; ?>
                </select><div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="cantidad_entregada">Cantidad entregada <span style="color:#f44336;">*</span></label>
                <input type="number" id="cantidad_entregada" name="cantidad_entregada" min="0.01" step="0.01" required data-rules="required|min_val:0.01"><div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="fecha_entrega">Fecha de entrega <span style="color:#f44336;">*</span></label>
                <input type="datetime-local" id="fecha_entrega" name="fecha_entrega" required value="<?= e(date('Y-m-d\TH:i')) ?>" data-rules="required"><div class="field-error"></div>
              </div>
              <div class="form-group full">
                <label for="evidencia_url">URL de evidencia</label>
                <input type="url" id="evidencia_url" name="evidencia_url" maxlength="255" placeholder="https://...">
              </div>
              <div class="form-group full">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="4"></textarea>
              </div>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
              <a href="<?= BASE_URL ?>/index.php?pagina=entregas" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary" <?= !$donaciones || !$beneficiarios ? 'disabled' : '' ?>>Registrar entrega</button>
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
