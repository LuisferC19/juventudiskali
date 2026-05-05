<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">
      <div class="card">
        <div class="card-header">
          <h3><?= isset($donador) ? 'Editar donador' : 'Nuevo donador' ?></h3>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=donadores&accion=<?= isset($donador) ? 'editar&id=' . e((string) $donador['id_donador']) : 'crear' ?>">
            <?php
              $tipoPersona = $donador['tipo_persona'] ?? 'fisica';
              $nombre = $donador['nombre'] ?? '';
              $apellido = $donador['apellido'] ?? '';
              $curp = $donador['curp'] ?? '';
              $fecha_nacimiento = $donador['fecha_nacimiento'] ?? '';
              $razon_social = $donador['razon_social'] ?? '';
              $rfc = $donador['rfc'] ?? '';
              $representante_legal = $donador['representante_legal'] ?? '';
              $giro_comercial = $donador['giro_comercial'] ?? '';
              $email = $donador['email'] ?? '';
              $telefono = $donador['telefono'] ?? '';
              $puntos_acumulados = $donador['puntos_acumulados'] ?? 0;
              $activo = isset($donador['activo']) && $donador['activo'];
            ?>

            <div class="form-grid">
              <div class="form-group full">
                <label for="tipo_persona">Tipo de Donador *</label>
                <select id="tipo_persona" name="tipo_persona" required onchange="toggleDonadorType()">
                  <option value="fisica" <?= $tipoPersona === 'fisica' ? 'selected' : '' ?>>Persona Física</option>
                  <option value="moral" <?= $tipoPersona === 'moral' ? 'selected' : '' ?>>Persona Moral</option>
                </select>
              </div>

              <!-- CAMPOS PERSONA FÍSICA -->
              <div id="fisica-fields" style="display: <?= $tipoPersona === 'fisica' ? 'block' : 'none' ?>; width: 100%;">
                <div class="form-group">
                  <label for="nombre">Nombre *</label>
                  <input type="text" id="nombre" name="nombre" value="<?= e($nombre) ?>" placeholder="Nombre" autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="apellido">Apellido *</label>
                  <input type="text" id="apellido" name="apellido" value="<?= e($apellido) ?>" placeholder="Apellido" autocomplete="off">
                </div>
                <div class="form-group full">
                  <label for="curp">CURP</label>
                  <input type="text" id="curp" name="curp" value="<?= e($curp) ?>" placeholder="CURP" autocomplete="off">
                </div>
                <div class="form-group full">
                  <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                  <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= e($fecha_nacimiento) ?>">
                </div>
              </div>

              <!-- CAMPOS PERSONA MORAL -->
              <div id="moral-fields" style="display: <?= $tipoPersona === 'moral' ? 'block' : 'none' ?>; width: 100%;">
                <div class="form-group full">
                  <label for="razon_social">Razón Social *</label>
                  <input type="text" id="razon_social" name="razon_social" value="<?= e($razon_social) ?>" placeholder="Razón social" autocomplete="off">
                </div>
                <div class="form-group full">
                  <label for="rfc">RFC</label>
                  <input type="text" id="rfc" name="rfc" value="<?= e($rfc) ?>" placeholder="RFC" autocomplete="off">
                </div>
                <div class="form-group full">
                  <label for="representante_legal">Representante Legal</label>
                  <input type="text" id="representante_legal" name="representante_legal" value="<?= e($representante_legal) ?>" placeholder="Representante legal" autocomplete="off">
                </div>
                <div class="form-group full">
                  <label for="giro_comercial">Giro Comercial</label>
                  <input type="text" id="giro_comercial" name="giro_comercial" value="<?= e($giro_comercial) ?>" placeholder="Giro comercial" autocomplete="off">
                </div>
              </div>

              <div class="form-group full"><label for="email">Email *</label><input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="correo@iskali.org" required></div>
              <div class="form-group"><label for="telefono">Teléfono</label><input type="tel" id="telefono" name="telefono" value="<?= e($telefono) ?>" placeholder="222-123-4567"></div>
              <div class="form-group"><label for="puntos_acumulados">Puntos Acumulados</label><input type="number" id="puntos_acumulados" min="0" name="puntos_acumulados" value="<?= e((string) $puntos_acumulados) ?>"></div>
              <div class="form-group form-checkbox">
                <label><input type="checkbox" name="activo" <?= $activo ? 'checked' : '' ?>> Activo</label>
              </div>
            </div>

            <div class="modal-footer" style="justify-content:flex-start;margin-top:1rem;">
              <a href="<?= BASE_URL ?>/index.php?pagina=donadores" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary">Guardar donador</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleDonadorType() {
  var tipo = document.getElementById('tipo_persona').value;
  document.getElementById('fisica-fields').style.display = tipo === 'fisica' ? 'block' : 'none';
  document.getElementById('moral-fields').style.display = tipo === 'moral' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
  toggleDonadorType();
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
