<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>
  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>
    <div class="content">
      <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="kpi"><div class="kpi-label">Total entregas</div><div class="kpi-value"><?= e((string)$total) ?></div></div>
        <div class="kpi amber"><div class="kpi-label">Programadas</div><div class="kpi-value"><?= e((string)$total_programadas) ?></div></div>
        <div class="kpi blue"><div class="kpi-label">En proceso</div><div class="kpi-value"><?= e((string)$total_en_proceso) ?></div></div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3>Entregas</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar entrega..." oninput="filtrarTabla(this,'tabla-entregas')">
            <?php if (usuarioPuede('entregas', 'crear')): ?>
              <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?pagina=entregas&accion=crear">+ Nueva entrega</a>
            <?php endif; ?>
          </div>
        </div>
        <?php if (!empty($mensaje)): ?>
          <div class="alert <?= $tipo_mensaje === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><?= e($mensaje) ?></div>
        <?php endif; ?>
        <table id="tabla-entregas">
          <thead><tr><th>ID</th><th>Donación</th><th>Donador</th><th>Beneficiario</th><th>Cantidad</th><th>Fecha entrega</th><th>Responsable</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php foreach ($entregas as $entrega): ?>
              <tr>
                <td>#<?= e((string)$entrega['id_entrega']) ?></td>
                <td><?= e($entrega['tipo_donacion'] . ' — ' . $entrega['campana']) ?></td>
                <td><?= e(trim($entrega['donador'] ?? '') ?: '—') ?></td>
                <td><?= e(trim($entrega['beneficiario'] ?? '') ?: '—') ?></td>
                <td><?= e(number_format((float)$entrega['cantidad_entregada'], 2)) ?> <?= e($entrega['unidad_medida'] ?? '') ?></td>
                <td><?= e($entrega['fecha_entrega']) ?></td>
                <td><?= e(trim($entrega['responsable'] ?? '') ?: '—') ?></td>
                <td><span class="badge <?= $entrega['estado'] === 'completada' ? 'badge-green' : ($entrega['estado'] === 'cancelada' ? 'badge-gray' : ($entrega['estado'] === 'en_proceso' ? 'badge-blue' : 'badge-amber')) ?>"><?= e(ucfirst(str_replace('_', ' ', $entrega['estado']))) ?></span></td>
                <td>
                  <?php if (usuarioPuede('entregas', 'editar') && !in_array($entrega['estado'], ['completada', 'cancelada'], true)): ?>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=entregas&accion=cambiar-estado&id=<?= e((string)$entrega['id_entrega']) ?>" style="display:flex;gap:6px;align-items:center;">
                      <?= csrfField() ?>
                      <select name="estado" aria-label="Nuevo estado de entrega">
                        <?php if ($entrega['estado'] === 'programada'): ?><option value="en_proceso">En proceso</option><?php endif; ?>
                        <option value="completada">Completada</option><option value="cancelada">Cancelada</option>
                      </select>
                      <button type="submit" class="btn btn-secondary btn-sm">Actualizar</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$entregas): ?><tr><td colspan="9" style="text-align:center;color:var(--muted);">No hay entregas registradas.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
