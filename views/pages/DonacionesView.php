<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>
  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>
    <div class="content">
      <div class="kpi-grid">
        <div class="kpi"><div class="kpi-label">Donaciones listadas</div><div class="kpi-value"><?= e((string)$total) ?></div></div>
        <div class="kpi amber"><div class="kpi-label">Pendientes</div><div class="kpi-value"><?= e((string)$total_pendientes) ?></div></div>
        <div class="kpi blue"><div class="kpi-label">Verificadas</div><div class="kpi-value"><?= e((string)$total_verificadas) ?></div></div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3>Donaciones</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar..." oninput="filtrarTabla(this,'tabla-donaciones')">
            <?php if (usuarioPuede('donaciones', 'crear')): ?>
              <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?pagina=donaciones&accion=crear">+ Nueva donación</a>
            <?php endif; ?>
          </div>
        </div>
        <?php if (!empty($mensaje)): ?>
          <div class="alert <?= $tipo_mensaje === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert"><?= e($mensaje) ?></div>
        <?php endif; ?>
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="toolbar" style="padding:16px;">
          <input type="hidden" name="pagina" value="donaciones">
          <label for="filtro-estado">Estado</label>
          <select id="filtro-estado" name="estado">
            <option value="">Todos</option>
            <?php foreach (['pendiente', 'recibida', 'verificada', 'rechazada'] as $opcion): ?>
              <option value="<?= e($opcion) ?>" <?= ($_GET['estado'] ?? '') === $opcion ? 'selected' : '' ?>><?= e(ucfirst($opcion)) ?></option>
            <?php endforeach; ?>
          </select>
          <label for="filtro-campana">Campaña</label>
          <select id="filtro-campana" name="id_campana">
            <option value="">Todas</option>
            <?php foreach ($campanas as $campana): ?>
              <option value="<?= e((string)$campana['id_campana']) ?>" <?= (string)($_GET['id_campana'] ?? '') === (string)$campana['id_campana'] ? 'selected' : '' ?>><?= e($campana['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-secondary">Filtrar</button>
        </form>
        <table id="tabla-donaciones">
          <thead><tr><th>ID</th><th>Donador</th><th>Tipo</th><th>Cantidad / monto</th><th>Fecha recepción</th><th>Campaña</th><th>Registrado por</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php foreach ($donaciones as $donacion): ?>
              <tr>
                <td>#<?= e((string)$donacion['id_donacion']) ?></td>
                <td><?= e(trim($donacion['donador'] ?? '') ?: '—') ?></td>
                <td><?= e($donacion['detalle']) ?></td>
                <td><?= e(number_format((float)$donacion['cantidad_monto'], 2)) ?> <?= e($donacion['unidad_medida'] ?? '') ?></td>
                <td><?= e($donacion['fecha_recepcion']) ?></td>
                <td><?= e($donacion['campana']) ?></td>
                <td><?= e(trim($donacion['registrador'] ?? '') ?: '—') ?></td>
                <td><span class="badge <?= $donacion['estado'] === 'verificada' ? 'badge-green' : ($donacion['estado'] === 'rechazada' ? 'badge-gray' : ($donacion['estado'] === 'recibida' ? 'badge-blue' : 'badge-amber')) ?>"><?= e(ucfirst($donacion['estado'])) ?></span></td>
                <td>
                  <?php if (usuarioPuede('donaciones', 'editar') && in_array($donacion['estado'], ['pendiente', 'recibida'], true)): ?>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=donaciones&accion=cambiar-estado&id=<?= e((string)$donacion['id_donacion']) ?>" style="display:flex;gap:6px;align-items:center;">
                      <?= csrfField() ?>
                      <select name="estado" aria-label="Nuevo estado de donación">
                        <?php if ($donacion['estado'] === 'pendiente'): ?>
                          <option value="recibida">Recibida</option><option value="rechazada">Rechazada</option>
                        <?php else: ?>
                          <option value="verificada">Verificada</option><option value="rechazada">Rechazada</option>
                        <?php endif; ?>
                      </select>
                      <button type="submit" class="btn btn-secondary btn-sm">Actualizar</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$donaciones): ?><tr><td colspan="9" style="text-align:center;color:var(--muted);">No hay donaciones para los filtros seleccionados.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
