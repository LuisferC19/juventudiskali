<?php
/**
 * views/layouts/sidebar.php
 * Barra lateral de navegación principal.
 * Usa $pagina_activa para marcar el ítem actual como active.
 */

// Helper: devuelve 'active' si la página coincide
function navActivo(string $pagina, string $actual): string {
    return $pagina === $actual ? 'active' : '';
}
?>

<nav class="sidebar">

  <!-- Logo -->
  <div class="sidebar-logo">
    <span>Iskalli</span>
    <small>Sistema Integral</small>
  </div>

  <!-- Menú de navegación -->
  <div class="nav-section">

    <div class="nav-label">Principal</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=dashboard" class="nav-item <?= navActivo('dashboard', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
      Dashboard
    </a>

    <div class="nav-label">Gestión</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=campanas" class="nav-item <?= navActivo('campanas', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1L14 5V13H2V5Z"/><rect x="6" y="9" width="4" height="4"/></svg>
      Campañas
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=donadores" class="nav-item <?= navActivo('donadores', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 2C9.1 2 10 2.9 10 4S9.1 6 8 6 6 5.1 6 4 6.9 2 8 2ZM14 13C14 10.2 11.3 9 8 9S2 10.2 2 13"/></svg>
      Donadores
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=donaciones" class="nav-item <?= navActivo('donaciones', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1V15M1 8H15" stroke-linecap="round"/></svg>
      Donaciones
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios" class="nav-item <?= navActivo('beneficiarios', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="5" r="3"/><path d="M1 14C1 11.2 3.2 10 6 10"/><circle cx="12" cy="8" r="2.5"/><path d="M9 14C9 12.1 10.3 11 12 11S15 12.1 15 14"/></svg>
      Beneficiarios
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=entregas" class="nav-item <?= navActivo('entregas', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 4L8 1L15 4V12L8 15L1 12V4Z"/><path d="M8 1V15M1 4L8 7L15 4"/></svg>
      Entregas
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=usuarios" class="nav-item <?= navActivo('usuarios', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="3"/><path d="M2 15C2 12.2 4.7 11 8 11S14 12.2 14 15"/></svg>
      Usuarios
    </a>

    <div class="nav-label">Control</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=inventario" class="nav-item <?= navActivo('inventario', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="14" height="9" rx="1"/><path d="M5 4V3A2 2 0 0 1 9 3V4M6 9H10"/></svg>
      Inventario
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=voluntarios" class="nav-item <?= navActivo('voluntarios', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 7L5 4M8 7L11 4M8 7V13"/><circle cx="8" cy="3" r="2"/><path d="M2 13H14"/></svg>
      Voluntarios
    </a>

    <div class="nav-label">Extras</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=gamificacion" class="nav-item <?= navActivo('gamificacion', $pagina_activa) ?>">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1L10 6H15L11 9.5L12.5 15L8 12L3.5 15L5 9.5L1 6H6Z"/></svg>
      Gamificación
    </a>

    <!-- Elementos sin página propia: muestran toast al hacer clic -->
    <a href="#" class="nav-item" onclick="showToast('Módulo GPS en construcción'); return false;">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><circle cx="8" cy="8" r="2"/><path d="M8 2V4M8 12V14M2 8H4M12 8H14"/></svg>
      Seguimiento GPS
    </a>
    <a href="#" class="nav-item" onclick="showToast('Módulo Quejas en construcción'); return false;">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 2H14V11H9L8 14L7 11H2Z"/></svg>
      Quejas / Sugerencias
    </a>
    <a href="#" class="nav-item" onclick="showToast('Módulo Notificaciones en construcción'); return false;">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1C5 1 3 3.5 3 6V10L1 12H15L13 10V6C13 3.5 11 1 8 1Z"/><path d="M6 12C6 13.1 6.9 14 8 14S10 13.1 10 12"/></svg>
      Notificaciones
    </a>
    <a href="#" class="nav-item" onclick="showToast('Módulo Historial en construcción'); return false;">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5V8L10 10"/></svg>
      Historial accesos
    </a>
    <a href="#" class="nav-item" onclick="showToast('Módulo Reportes en construcción'); return false;">
      <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4H14M2 8H10M2 12H7" stroke-linecap="round"/></svg>
      Reportes
    </a>

  </div><!-- /nav-section -->

  <!-- Chip de usuario al pie del sidebar -->
  <div class="sidebar-bottom">
    <div class="user-chip">
      <div class="user-avatar">ES</div>
      <div class="user-info">
        <span><?= htmlspecialchars($_SESSION['usuario'] ?? ADMIN_NOMBRE) ?></span>
        <small><?= htmlspecialchars($_SESSION['rol'] ?? ADMIN_ROL) ?></small>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/index.php?pagina=logout" class="btn btn-secondary btn-sm" style="width:100%;margin-top:.6rem;font-size:10.5px;text-align:center;text-decoration:none;">
      Cerrar sesión
    </a>
  </div>

</nav><!-- /sidebar -->