<?php require_once 'views/layouts/header.php'; ?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Total notificaciones</div>
          <div class="kpi-value"><?= count($notificaciones) ?></div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">No leídas</div>
          <div class="kpi-value"><?= $total_no_leidas ?></div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">Leídas</div>
          <div class="kpi-value"><?= count($notificaciones) - $total_no_leidas ?></div>
        </div>
      </div>

      <!-- Tabla de notificaciones -->
      <div class="card">
        <div class="card-header">
          <h3>Mis Notificaciones</h3>
          <?php if ($total_no_leidas > 0): ?>
            <button class="btn btn-secondary" onclick="marcarTodasLeidas()">
              Marcar todas como leídas
            </button>
          <?php endif; ?>
        </div>

        <?php if (empty($notificaciones)): ?>
          <div style="padding:40px;text-align:center;color:var(--muted);">
            <p style="font-size:14px;margin-bottom:10px;">No tienes notificaciones aún</p>
            <p style="font-size:12px;">Aquí aparecerán tus notificaciones del sistema</p>
          </div>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th style="width:50px;">Leída</th>
                <th>Tipo</th>
                <th>Asunto</th>
                <th>Mensaje</th>
                <th>Canal</th>
                <th>Fecha</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($notificaciones as $notif): ?>
                <tr style="background:<?= $notif['leida'] ? '#fff' : 'rgba(10,175,160,.05)' ?>;">
                  <td>
                    <?php if (!$notif['leida']): ?>
                      <span class="badge badge-teal">Nuevo</span>
                    <?php else: ?>
                      <span style="font-size:11px;color:var(--muted);">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                      $tipo = $notif['tipo'] ?? 'info';
                      $color = match($tipo) {
                        'error' => 'badge-danger',
                        'warning' => 'badge-amber',
                        'success' => 'badge-green',
                        'info' => 'badge-blue',
                        default => 'badge-gray',
                      };
                    ?>
                    <span class="badge <?= $color ?>" style="text-transform:capitalize;">
                      <?= htmlspecialchars($tipo) ?>
                    </span>
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($notif['asunto'] ?? '(Sin asunto)') ?></strong>
                  </td>
                  <td style="max-width:300px;color:var(--muted);font-size:13px;">
                    <?= htmlspecialchars(substr($notif['mensaje'] ?? '', 0, 80)) ?>...
                  </td>
                  <td>
                    <span style="font-size:11px;background:var(--border);padding:2px 6px;border-radius:4px;">
                      <?= htmlspecialchars($notif['canal'] ?? 'web') ?>
                    </span>
                  </td>
                  <td style="font-size:12px;color:var(--muted);">
                    <?= date('d/m/Y H:i', strtotime($notif['fecha_creacion'])) ?>
                  </td>
                  <td>
                    <?php if (!$notif['leida']): ?>
                      <button class="btn btn-xs btn-secondary" 
                              onclick="marcarLeidaIndividual(<?= (int)$notif['id_notificacion'] ?>)">
                        Marcar leída
                      </button>
                    <?php else: ?>
                      <span style="font-size:11px;color:var(--muted);">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<script>
function marcarLeidaIndividual(id) {
  fetch('<?= BASE_URL ?>/index.php?pagina=notificaciones&accion=marcar_leida', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + id
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast('Notificación marcada como leída');
      setTimeout(() => location.reload(), 500);
    }
  })
  .catch(e => showToast('Error al marcar la notificación', 'error'));
}

function marcarTodasLeidas() {
  fetch('<?= BASE_URL ?>/index.php?pagina=notificaciones&accion=marcar_todas', {
    method: 'POST'
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast('Todas marcadas como leídas');
      setTimeout(() => location.reload(), 500);
    }
  })
  .catch(e => showToast('Error al marcar las notificaciones', 'error'));
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
