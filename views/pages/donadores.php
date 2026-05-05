<?php
/**
 * views/pages/donadores.php
 * Listado y gestión de donadores.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <div class="card">
        <div class="card-header">
          <h3>Donadores</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar donador..." oninput="filtrarTabla(this,'tabla-donadores')">
            <a href="<?= BASE_URL ?>/index.php?pagina=donadores&accion=crear" class="btn btn-primary">+ Nuevo donador</a>
          </div>
        </div>

        <?php if (!empty($mensaje)): ?>
          <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <table id="tabla-donadores">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Tipo</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Puntos</th>
              <th>Nivel</th>
              <th>Activo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($donadores as $donador): ?>
              <tr>
                <td><?= e((string) $donador['id_donador']) ?></td>
                <td><?= e($donador['nombre_completo']) ?></td>
                <td><?= e(ucfirst($donador['tipo_persona'])) ?></td>
                <td><?= e($donador['email']) ?></td>
                <td><?= e($donador['telefono'] ?? '-') ?></td>
                <td><?= e((string) $donador['puntos_acumulados']) ?></td>
                <td><?= e($donador['nivel'] ?? '—') ?></td>
                <td><?= $donador['activo'] ? 'Sí' : 'No' ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/index.php?pagina=donadores&accion=editar&id=<?= e((string) $donador['id_donador']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                  <a href="<?= BASE_URL ?>/index.php?pagina=donadores&accion=eliminar&id=<?= e((string) $donador['id_donador']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este donador?');">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
