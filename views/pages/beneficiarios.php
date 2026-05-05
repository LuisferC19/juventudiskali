<?php
/**
 * views/pages/beneficiarios.php
 * Registro de beneficiarios del programa.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- Tabla de beneficiarios -->
      <div class="card">
        <div class="card-header">
          <h3>Beneficiarios</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar beneficiario..." oninput="filtrarTabla(this,'tabla-beneficiarios')">
            <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios&accion=nuevo" class="btn btn-primary">+ Registrar</a>
          </div>
        </div>

        <?php if (!empty($mensaje)): ?>
          <div class="alert <?= isset($tipo_mensaje) && $tipo_mensaje === 'error' ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= htmlspecialchars($mensaje) ?>
          </div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
          <table id="tabla-beneficiarios" style="width:100%;border-collapse:collapse;">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Edad</th>
                <th>Comunidad</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Registrado por</th>
                <th>Creado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($beneficiarios)): ?>
                <tr>
                  <td colspan="9" style="padding:24px;text-align:center;color:var(--muted,#999);">No hay beneficiarios registrados.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($beneficiarios as $beneficiario): ?>
                  <tr style="border-bottom:1px solid var(--border,#eee);">
                    <td style="padding:12px;">#<?= e((string)$beneficiario['id_beneficiario']) ?></td>
                    <td style="padding:12px;"><?= e($beneficiario['nombre_completo']) ?></td>
                    <td style="padding:12px;"><?= e($beneficiario['edad'] ?? '—') ?></td>
                    <td style="padding:12px;"><?= e($beneficiario['comunidad'] ?? '—') ?></td>
                    <td style="padding:12px;"><?= e($beneficiario['telefono'] ?? '—') ?></td>
                    <td style="padding:12px;">
                      <?php if ($beneficiario['estado'] === 'activo'): ?>
                        <span class="badge badge-green">Activo</span>
                      <?php elseif ($beneficiario['estado'] === 'en_espera'): ?>
                        <span class="badge badge-amber">En espera</span>
                      <?php else: ?>
                        <span class="badge badge-red">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td style="padding:12px;"><?= e($beneficiario['registrado_por'] ?? '—') ?></td>
                    <td style="padding:12px;"><?= isset($beneficiario['created_at']) ? date('d/m/Y', strtotime($beneficiario['created_at'])) : '—' ?></td>
                    <td style="padding:12px;white-space:nowrap;">
                      <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios&accion=editar&id=<?= e((string) $beneficiario['id_beneficiario']) ?>" class="btn btn-secondary btn-sm" style="margin:2px;">Editar</a>
                      <a href="<?= BASE_URL ?>/index.php?pagina=beneficiarios&accion=borrar&id=<?= e((string) $beneficiario['id_beneficiario']) ?>" class="btn btn-danger btn-sm" style="margin:2px;" onclick="return confirm('¿Eliminar este beneficiario?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>