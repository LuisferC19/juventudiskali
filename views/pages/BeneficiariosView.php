<?php
/**
 * views/pages/BeneficiariosView.php
 * Registro de beneficiarios del programa.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- Tarjetas de estadísticas -->
      <div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:var(--primary);"><?= e($total) ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Beneficiarios totales</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#4caf50;"><?= e($total_activos) ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Beneficiarios activos</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#f44336;"><?= e($total_inactivos) ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Beneficiarios inactivos</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#3f51b5;"><?= e($total_fisica) ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Personas físicas</div>
        </div>

        <div class="card" style="flex:1;min-width:150px;padding:20px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#ff9800;"><?= e($total_moral) ?></div>
          <div style="font-size:13px;color:var(--muted);margin-top:4px;">Personas morales</div>
        </div>

      </div>

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
                <th>Tipo</th>
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
                  <td colspan="10" style="padding:24px;text-align:center;color:var(--muted,#999);">No hay beneficiarios registrados.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($beneficiarios as $beneficiario): ?>
                  <tr style="border-bottom:1px solid var(--border,#eee);">
                    <td style="padding:12px;">#<?= e((string)$beneficiario['id_beneficiario']) ?></td>
                    <td style="padding:12px;"><?= e($beneficiario['nombre_completo']) ?></td>
                    <td style="padding:12px;"><?= e(ucfirst($beneficiario['tipo_persona'])) ?></td>
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
                      <form method="POST"
                            action="<?= BASE_URL ?>/index.php?pagina=beneficiarios&accion=borrar&id=<?= e((string) $beneficiario['id_beneficiario']) ?>"
                            style="display:inline;"
                            onsubmit="return confirm('¿Eliminar este beneficiario?');">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-danger btn-sm" style="margin:2px;">Eliminar</button>
                      </form>
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
