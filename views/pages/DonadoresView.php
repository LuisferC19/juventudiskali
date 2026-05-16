<?php
/**
 * views/pages/DonadoresView.php
 * Listado y gestión de donadores.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- ===== Tarjetas de estadísticas ===== -->
      <div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:var(--primary);"><?= $total ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Donadores totales</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#4caf50;"><?= $total_activos ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Donadores activos</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#f44336;"><?= $total - $total_activos ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Donadores inactivos</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#2196f3;"><?= $total_por_tipo['Física'] ?? 0 ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Personas físicas</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#ff9800;"><?= $total_por_tipo['Moral'] ?? 0 ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Personas morales</div>
        </div>

      </div>

      <div class="card">
        <div class="card-header">
          <h3>Donadores</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar donador..." oninput="filtrarTabla(this,'tabla-donadores')">
            <a href="<?= BASE_URL ?>/index.php?pagina=donadores&accion=crear" class="btn btn-primary">+ Nuevo donador</a>
          </div>
        </div>

        <?php $tipo_mensaje = $tipo_mensaje ?? 'success'; ?>
        <?php if (!empty($mensaje)): ?>
          <div class="alert <?= $tipo_mensaje === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= htmlspecialchars($mensaje) ?>
          </div>
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
                  <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=donadores&accion=eliminar&id=<?= e((string) $donador['id_donador']) ?>" style="display:inline; margin:0;">
                    <button type="submit" class="btn btn-danger btn-sm" style="margin:2px;" onclick="return confirm('¿Eliminar este donador?');">Eliminar</button>
                  </form>
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
