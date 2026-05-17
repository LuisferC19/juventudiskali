<?php
/**
 * views/layouts/topbar.php
 * Barra superior con título de página, fecha y avatar.
 */

// Obtener conteo de notificaciones no leídas
$notif_count = 0;
if (!empty($_SESSION['id_usuario']) && isset($conexion)) {
    require_once 'models/NotificacionModel.php';
    $modelo_notif = new NotificacionModel($conexion);
    $notif_count = $modelo_notif->contarNoLeidas((int)$_SESSION['id_usuario'], 'web');
}
?>
<div class="topbar">
  <!-- Título dinámico de la página actual -->
  <div class="topbar-title"><?= htmlspecialchars($titulo_pagina ?? 'Dashboard') ?></div>

  <div class="topbar-actions">
    <a href="<?= BASE_URL ?>/index.php?pagina=dashboard" class="topbar-button btn-icon">
     <img src="<?= BASE_URL ?>/public/iconos/Dashboard.png" alt="Dashboard" style="width:16px;height:16px;">
      Inicio
    </a>

    <!-- Fecha actual -->
    <span style="font-size:11px;color:var(--muted);">
      <?= date('D d M Y') ?>
    </span>

    <div style="width:1px;height:16px;background:var(--border);"></div>

    <!-- Ícono de notificación con badge -->
    <div style="position:relative;cursor:pointer;" title="Notificaciones" onclick="toggleNotifDropdown()">
      <img src="<?= BASE_URL ?>/public/iconos/notificacion.png" alt="Notificaciones" style="width:16px;height:16px;cursor:pointer;">
      <?php if ($notif_count > 0): ?>
        <span style="position:absolute;top:-3px;right:-3px;width:18px;height:18px;background:var(--danger);border-radius:50%;border:1.5px solid #fff;display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:700;">
          <?= min($notif_count, 9) ?>
        </span>
      <?php endif; ?>
      
      <!-- Dropdown de notificaciones -->
      <div id="notif-dropdown" class="notif-dropdown" style="display:none;position:absolute;top:100%;right:0;background:#fff;border:1px solid var(--border);border-radius:8px;width:320px;max-height:400px;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,.1);z-index:1000;margin-top:8px;">
        <div style="padding:12px;border-bottom:1px solid var(--border);">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <strong style="font-size:13px;">Notificaciones</strong>
            <?php if ($notif_count > 0): ?>
              <small style="color:var(--muted);font-size:11px;cursor:pointer;" onclick="marcarTodasNotificacionesLeidas()">Marcar todas leídas</small>
            <?php endif; ?>
          </div>
        </div>
        <div id="notif-list" style="max-height:300px;overflow-y:auto;"></div>
        <div style="padding:10px;border-top:1px solid var(--border);text-align:center;">
          <a href="<?= BASE_URL ?>/index.php?pagina=notificaciones" style="font-size:12px;color:var(--accent);text-decoration:none;">Ver todas las notificaciones</a>
        </div>
      </div>
    </div>

    <a href="<?= BASE_URL ?>/index.php?pagina=logout" class="topbar-button btn-icon">
     <img src="<?= BASE_URL ?>/public/iconos/cerrar_sesion_puerta.png" alt="Cerrar sesión" style="width:16px;height:16px;">
      Salir
    </a>

    <!-- Avatar del usuario con sus iniciales reales -->
    <?php
      $nombreCompleto = $_SESSION['usuario'] ?? 'Usuario';
      $partes = explode(' ', trim($nombreCompleto));
      $iniciales = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
    ?>
    <div style="width:28px;height:28px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:600;" title="<?= htmlspecialchars($nombreCompleto) ?>">
      <?= htmlspecialchars($iniciales) ?>
    </div>
  </div>
</div>

<script>
function toggleNotifDropdown() {
  const dropdown = document.getElementById('notif-dropdown');
  if (dropdown.style.display === 'none') {
    dropdown.style.display = 'block';
    cargarNotificacionesDropdown();
  } else {
    dropdown.style.display = 'none';
  }
}

function cargarNotificacionesDropdown() {
  fetch('<?= BASE_URL ?>/index.php?pagina=notificaciones&accion=ultimas')
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('notif-list');
      if (!data.notificaciones || data.notificaciones.length === 0) {
        list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted);font-size:12px;">No tienes notificaciones</div>';
        return;
      }
      list.innerHTML = data.notificaciones.map(n => `
        <div style="padding:10px 12px;border-bottom:1px solid var(--border);cursor:pointer;" onclick="abrirNotificacion(${n.id_notificacion})">
          <div style="display:flex;justify-content:space-between;align-items:start;">
            <strong style="font-size:12px;color:var(--text);">${n.asunto}</strong>
            <span style="font-size:10px;color:var(--muted);">${new Date(n.fecha_creacion).toLocaleDateString('es-MX')}</span>
          </div>
          <div style="font-size:11px;color:var(--muted);margin-top:4px;line-height:1.4;">${n.mensaje.substring(0, 60)}...</div>
        </div>
      `).join('');
    })
    .catch(e => console.error('Error cargando notificaciones:', e));
}

function marcarTodasNotificacionesLeidas() {
  fetch('<?= BASE_URL ?>/index.php?pagina=notificaciones&accion=marcar_todas', {
    method: 'POST'
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('notif-dropdown').style.display = 'none';
      location.reload();
    }
  })
  .catch(e => console.error('Error:', e));
}

function abrirNotificacion(id) {
  window.location.href = '<?= BASE_URL ?>/index.php?pagina=notificaciones';
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('notif-dropdown');
  if (!e.target.closest('.topbar-actions')) {
    dropdown.style.display = 'none';
  }
});
</script>