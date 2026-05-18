<?php
/**
 * views/layouts/sidebar.php
 *
 * CORRECCIONES:
 * - Se eliminaron los módulos "Seguimiento GPS" y "Quejas y Sugerencias"
 *   que eran stubs en construcción (showToast) y no deben aparecer en producción.
 * - Se resolvieron los conflictos de merge de Git (<<<<<<< HEAD / >>>>>>>)
 *   que quedaron sin resolver en el archivo original.
 * - El enlace "Reportes" apunta correctamente a ?pagina=reportes.
 */

function navActivo(string $pagina, string $actual): string
{
    return $pagina === $actual ? 'active' : '';
}
?>

<nav class="sidebar">

  <!-- Logo con mascota -->
  <div class="sidebar-logo">
    <img src="<?= BASE_URL ?>/public/img/Mascota_Iski_1.jpeg"
         alt="Iskali Mascota"
         style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-bottom:8px;">
    <span>Juventud Iskali</span>
    <small>Sistema Integra</small>
  </div>

  <!-- Menú de navegación -->
  <div class="nav-section">

    <div class="nav-label">Principal</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=dashboard"
       class="nav-item <?= navActivo('dashboard', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Dashboard.png" alt="Dashboard" style="width:20px;height:20px;">
      Dashboard
    </a>

    <div class="nav-label">Gestión</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=campanas"
       class="nav-item <?= navActivo('campanas', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Campanas.png" alt="Campañas" style="width:20px;height:20px;">
      Campañas
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=donadores"
       class="nav-item <?= navActivo('donadores', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/patrocinador.png" alt="Donadores" style="width:20px;height:20px;">
      Donadores
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=donaciones"
       class="nav-item <?= navActivo('donaciones', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/donaciones.png" alt="Donaciones" style="width:20px;height:20px;">
      Donaciones
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios"
       class="nav-item <?= navActivo('beneficiarios', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Beneficiarios.png" alt="Beneficiarios" style="width:20px;height:20px;">
      Beneficiarios
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=entregas"
       class="nav-item <?= navActivo('entregas', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Entregas.png" alt="Entregas" style="width:20px;height:20px;">
      Entregas
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=usuarios"
       class="nav-item <?= navActivo('usuarios', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Usuarios.png" alt="Usuarios" style="width:20px;height:20px;">
      Usuarios
    </a>

    <div class="nav-label">Control</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=inventario"
       class="nav-item <?= navActivo('inventario', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/inventario.png" alt="Inventario" style="width:20px;height:20px;">
      Inventario
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=voluntarios"
       class="nav-item <?= navActivo('voluntarios', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Voluntarios.png" alt="Voluntarios" style="width:20px;height:20px;">
      Voluntarios
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=respaldos"
       class="nav-item <?= navActivo('respaldos', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/respaldo_base_datos.png" alt="Respaldos" style="width:20px;height:20px;">
      Respaldos
    </a>

    <div class="nav-label">Extras</div>
    <a href="<?= BASE_URL ?>/index.php?pagina=gamificacion"
       class="nav-item <?= navActivo('gamificacion', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/gamificacion_trofeo.png" alt="Gamificación" style="width:20px;height:20px;">
      Gamificación
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=planning"
       class="nav-item <?= navActivo('planning', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/planificacion.png" alt="Planning" style="width:20px;height:20px;">
      Planning
    </a>
    <a href="<?= BASE_URL ?>/index.php?pagina=reportes"
       class="nav-item <?= navActivo('reportes', $pagina_activa) ?>">
      <img src="<?= BASE_URL ?>/public/iconos/Reportes.png" alt="Reportes" style="width:20px;height:20px;">
      Reportes
    </a>

    <!-- Módulos en construcción -->
    <a href="#" class="nav-item" onclick="showToast('Módulo Notificaciones en construcción'); return false;">
      <img src="<?= BASE_URL ?>/public/iconos/Modulo_Notificaciones.png" alt="Notificaciones" style="width:20px;height:20px;">
      Notificaciones
    </a>
    <a href="#" class="nav-item" onclick="showToast('Módulo Historial en construcción'); return false;">
      <img src="<?= BASE_URL ?>/public/iconos/Historial_De_Acesso.png" alt="Historial" style="width:20px;height:20px;">
      Historial accesos
    </a>

  </div><!-- /nav-section -->

  <!-- Chip de usuario al pie del sidebar -->
  <div class="sidebar-bottom">
    <div class="user-chip">
      <div class="user-avatar">ES</div>
      <div class="user-info">
        <span><?= htmlspecialchars($_SESSION['usuario'] ?? ADMIN_NOMBRE) ?></span>
        <small><?= htmlspecialchars($_SESSION['rol']    ?? ADMIN_ROL) ?></small>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/index.php?pagina=logout"
       class="btn btn-secondary btn-sm"
       style="width:100%;margin-top:.6rem;font-size:10.5px;text-align:center;text-decoration:none;">
      Cerrar sesión
    </a>
  </div>

</nav><!-- /sidebar -->