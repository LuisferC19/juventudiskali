<?php
/**
 * views/layouts/topbar.php
 * Barra superior con título de página, fecha y avatar.
 */
?>
<div class="topbar">
  <!-- Título dinámico de la página actual -->
  <div class="topbar-title"><?= htmlspecialchars($titulo_pagina ?? 'Dashboard') ?></div>

  <div class="topbar-actions">
    <a href="<?= BASE_URL ?>/index.php?pagina=dashboard" class="topbar-button btn-icon">
      <img src="<?= BASE_URL ?>/public/iconos/icons8-casa-50.png" alt="Dashboard" class="icon-img">
      Inicio
    </a>

    <!-- Fecha actual -->
    <span style="font-size:11px;color:var(--muted);">
      <?= date('D d M Y') ?>
    </span>

    <div style="width:1px;height:16px;background:var(--border);"></div>

    <!-- Ícono de notificación con punto rojo -->
    <div style="position:relative;cursor:pointer;" title="Notificaciones">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="var(--muted)" stroke-width="1.5">
        <path d="M8 1C5 1 3 3.5 3 6V10L1 12H15L13 10V6C13 3.5 11 1 8 1Z"/>
        <path d="M6 12C6 13.1 6.9 14 8 14S10 13.1 10 12"/>
      </svg>
      <span style="position:absolute;top:-3px;right:-3px;width:8px;height:8px;background:var(--danger);border-radius:50%;border:1.5px solid #fff;"></span>
    </div>

    <a href="<?= BASE_URL ?>/index.php?pagina=logout" class="topbar-button btn-icon">
      <img src="<?= BASE_URL ?>/public/iconos/icons8-cancelar-50.png" alt="Cerrar sesión" class="icon-img">
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