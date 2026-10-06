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
        <a href="<?= BASE_URL ?>/index.php?pagina=donaciones" style="color:var(--primary);text-decoration:none;">Donaciones</a>
        <span style="margin:0 8px;opacity:.5;">›</span>Nueva donación
      </div>
      <div class="card" style="max-width:900px;">
        <div class="card-header"><h3>Nueva donación</h3></div>
        <div class="card-body" style="padding:24px;">
          <?php if ($errorFormulario): ?><div class="alert <?= $tipoErrorFormulario === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><?= e($errorFormulario) ?></div><?php endif; ?>
          <?php if (!$donadores || !$campanas): ?>
            <div class="alert alert-danger" role="alert">Para registrar una donación se requiere al menos un donador activo y una campaña activa.</div>
          <?php endif; ?>
          <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=donaciones&accion=guardar" id="form-donacion" data-validate="true">
            <?= csrfField() ?>
            <div class="form-grid">
              <div class="form-group">
                <label for="id_donador">Donador <span style="color:#f44336;">*</span></label>
                <select id="id_donador" name="id_donador" required data-rules="required">
                  <option value="">Selecciona un donador</option>
                  <?php foreach ($donadores as $donador): ?><option value="<?= e((string)$donador['id_donador']) ?>"><?= e($donador['nombre']) ?></option><?php endforeach; ?>
                </select><div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="id_campana">Campaña activa <span style="color:#f44336;">*</span></label>
                <select id="id_campana" name="id_campana" required data-rules="required">
                  <option value="">Selecciona una campaña</option>
                  <?php foreach ($campanas as $campana): ?><option value="<?= e((string)$campana['id_campana']) ?>"><?= e($campana['nombre']) ?></option><?php endforeach; ?>
                </select><div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="fecha_recepcion">Fecha de recepción <span style="color:#f44336;">*</span></label>
                <input type="date" id="fecha_recepcion" name="fecha_recepcion" required value="<?= e(date('Y-m-d')) ?>" data-rules="required|date"><div class="field-error"></div>
              </div>
              <div class="form-group">
                <label for="evidencia_url">URL de evidencia</label>
                <input type="url" id="evidencia_url" name="evidencia_url" maxlength="255" placeholder="https://...">
              </div>
              <div class="form-group full">
                <label for="tipo">Tipo de donación <span style="color:#f44336;">*</span></label>
                <select id="tipo" name="tipo" required onchange="toggleDonationType()" data-rules="required">
                  <option value="especie">En especie</option><option value="economica">Económica</option>
                </select><div class="field-error"></div>
              </div>
            </div>

            <div id="especie-fields" style="display:block;background:rgba(10,175,160,.04);border:1px solid rgba(10,175,160,.12);border-radius:10px;padding:20px;margin:20px 0;">
              <div style="font-size:12px;font-weight:600;color:var(--primary);letter-spacing:.5px;text-transform:uppercase;margin-bottom:16px;">Detalle de donación en especie</div>
              <div class="form-grid">
                <div class="form-group">
                  <label for="id_tipo_bien">Tipo de bien <span style="color:#f44336;">*</span></label>
                  <select id="id_tipo_bien" name="id_tipo_bien" required data-rules="required" onchange="actualizarUnidad()">
                    <option value="">Selecciona un bien</option>
                    <?php foreach ($tipos_bien as $bien): ?><option value="<?= e((string)$bien['id_tipo_bien']) ?>" data-unidad="<?= e($bien['unidad_medida']) ?>"><?= e($bien['nombre']) ?></option><?php endforeach; ?>
                  </select><div class="field-error"></div>
                </div>
                <div class="form-group">
                  <label for="cantidad">Cantidad <span style="color:#f44336;">*</span></label>
                  <input type="number" id="cantidad" name="cantidad" min="0.01" step="0.01" required data-rules="required|min_val:0.01"><div class="field-error"></div>
                </div>
                <div class="form-group">
                  <label for="unidad_medida">Unidad de medida</label>
                  <input type="text" id="unidad_medida" name="unidad_medida" readonly>
                </div>
                <div class="form-group full">
                  <label for="descripcion">Descripción</label>
                  <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
              </div>
            </div>

            <div id="economica-fields" style="display:none;background:rgba(232,160,32,.04);border:1px solid rgba(232,160,32,.15);border-radius:10px;padding:20px;margin:20px 0;">
              <div style="font-size:12px;font-weight:600;color:var(--amber);letter-spacing:.5px;text-transform:uppercase;margin-bottom:16px;">Detalle de donación económica</div>
              <div class="form-grid">
                <div class="form-group">
                  <label for="monto">Monto <span style="color:#f44336;">*</span></label>
                  <input type="number" id="monto" name="monto" min="0.01" step="0.01" data-rules=""><div class="field-error"></div>
                </div>
                <div class="form-group">
                  <label for="moneda">Moneda</label>
                  <input type="text" id="moneda" name="moneda" value="MXN" maxlength="3" pattern="[A-Za-z]{3}">
                </div>
                <div class="form-group">
                  <label for="metodo_pago">Método de pago <span style="color:#f44336;">*</span></label>
                  <select id="metodo_pago" name="metodo_pago" data-rules="">
                    <option value="">Selecciona</option><option value="efectivo">Efectivo</option><option value="transferencia">Transferencia</option><option value="cheque">Cheque</option><option value="otro">Otro</option>
                  </select><div class="field-error"></div>
                </div>
                <div class="form-group">
                  <label for="referencia_pago">Referencia de pago</label>
                  <input type="text" id="referencia_pago" name="referencia_pago" maxlength="100">
                </div>
              </div>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
              <a href="<?= BASE_URL ?>/index.php?pagina=donaciones" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary" <?= (!$donadores || !$campanas) ? 'disabled' : '' ?>>Registrar donación</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= BASE_URL ?>/public/js/App.js"></script>
<script src="<?= BASE_URL ?>/public/js/Validaciones.js"></script>
<script>
function toggleDonationType() {
  const tipo = document.getElementById('tipo').value;
  const especie = tipo === 'especie';
  document.getElementById('especie-fields').style.display = especie ? 'block' : 'none';
  document.getElementById('economica-fields').style.display = especie ? 'none' : 'block';
  document.getElementById('id_tipo_bien').dataset.rules = especie ? 'required' : '';
  document.getElementById('cantidad').dataset.rules = especie ? 'required|min_val:0.01' : '';
  document.getElementById('monto').dataset.rules = especie ? '' : 'required|min_val:0.01';
  document.getElementById('metodo_pago').dataset.rules = especie ? '' : 'required';
  document.getElementById('id_tipo_bien').disabled = !especie;
  document.getElementById('cantidad').disabled = !especie;
  document.getElementById('monto').disabled = especie;
  document.getElementById('metodo_pago').disabled = especie;
  document.getElementById('descripcion').disabled = !especie;
  document.getElementById('unidad_medida').disabled = !especie;
  document.getElementById('moneda').disabled = especie;
  document.getElementById('referencia_pago').disabled = especie;
}
function actualizarUnidad() {
  const option = document.getElementById('id_tipo_bien').selectedOptions[0];
  document.getElementById('unidad_medida').value = option ? (option.dataset.unidad || '') : '';
}
document.addEventListener('DOMContentLoaded', () => { toggleDonationType(); actualizarUnidad(); });
</script>
<?php require_once 'views/layouts/footer.php'; ?>
