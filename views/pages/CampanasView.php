<?php
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>
  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>
    <div class="content">
      <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="kpi">
          <div class="kpi-label">Total</div>
          <div class="kpi-value"><?= e((string)$total) ?></div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Activas</div>
          <div class="kpi-value"><?= e((string)$total_activas) ?></div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">Cerradas</div>
          <div class="kpi-value"><?= e((string)$total_cerradas) ?></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>Campañas</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar campaña..." oninput="filtrarTabla(this,'tabla-campanas')">
            <?php if (usuarioPuede('campanas', 'crear')): ?>
              <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?pagina=campanas&accion=crear">+ Nueva campaña</a>
            <?php endif; ?>
          </div>
        </div>
        <?php if (!empty($mensaje)): ?>
          <div class="alert <?= $tipo_mensaje === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <table id="tabla-campanas">
          <thead>
            <tr><th>ID</th><th>Nombre</th><th>Tipo meta</th><th>Meta económica</th><th>Inicio</th><th>Cierre</th><th>Estado</th><th>Creada por</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            <?php foreach ($campanas as $campana): ?>
              <tr>
                <td>#<?= e((string)$campana['id_campana']) ?></td>
                <td><?= e($campana['nombre']) ?></td>
                <td><?= e(ucfirst($campana['tipo_meta'])) ?></td>
                <td><?= e(number_format((float)$campana['meta_economica'], 2)) ?></td>
                <td><?= e($campana['fecha_inicio']) ?></td>
                <td><?= e($campana['fecha_cierre']) ?></td>
                <td><span class="badge <?= $campana['estado'] === 'activa' ? 'badge-green' : ($campana['estado'] === 'cancelada' ? 'badge-gray' : 'badge-amber') ?>"><?= e(ucfirst($campana['estado'])) ?></span></td>
                <td><?= e(trim($campana['creador'] ?? '') ?: '—') ?></td>
                <td style="white-space:nowrap;">
                  <?php if (usuarioPuede('campanas', 'editar')): ?>
                    <a class="btn btn-secondary btn-sm" href="<?= BASE_URL ?>/index.php?pagina=campanas&accion=editar&id=<?= e((string)$campana['id_campana']) ?>">Editar</a>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=campanas&accion=cambiar-estado&id=<?= e((string)$campana['id_campana']) ?>" style="display:inline;">
                      <?= csrfField() ?>
                      <select name="estado" aria-label="Cambiar estado de campaña">
                        <?php foreach (['borrador', 'activa', 'pausada', 'cerrada', 'cancelada'] as $estado): ?>
                          <option value="<?= e($estado) ?>" <?= $campana['estado'] === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="btn btn-secondary btn-sm">Cambiar</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$campanas): ?><tr><td colspan="9" style="text-align:center;color:var(--muted);">No hay campañas registradas.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
