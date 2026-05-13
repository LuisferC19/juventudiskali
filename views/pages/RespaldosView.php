<?php
/**
 * views/pages/RespaldosView.php
 * VERSION MEJORADA: historial de BD, importación ZIP, SweetAlert2.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content" style="padding:32px;background:var(--fondo,#f8fffe);min-height:calc(100vh - 72px);">

      <!-- ══ Encabezado ══ -->
      <div style="margin-bottom:28px;">
        <h2 style="font-family:'Syne',sans-serif;font-size:24px;font-weight:700;letter-spacing:-0.5px;color:var(--texto,#1a2e2c);margin-bottom:8px;">
          Respaldos de Base de Datos 💾
        </h2>
        <p style="font-size:14px;color:var(--muted,#5a8a84);max-width:620px;line-height:1.7;">
          Genera respaldos comprimidos (.zip) o restaura la base de datos desde un respaldo previo.
          El historial de todas las operaciones queda registrado automáticamente.
        </p>
      </div>

      <!-- ══ Tarjetas de información ══ -->
      <div style="display:flex;gap:20px;margin-bottom:28px;flex-wrap:wrap;">

        <div class="card" style="flex:1;min-width:200px;padding:22px;text-align:center;">
          <div style="font-size:30px;font-weight:700;color:var(--verde,#0aafa0);">
            <?= count($historial ?? []) ?>
          </div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Operaciones registradas</div>
        </div>

        <div class="card" style="flex:1;min-width:200px;padding:22px;text-align:center;">
          <div style="font-size:30px;font-weight:700;color:#4caf50;">🗄️</div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Base de datos: iskali</div>
        </div>

        <div class="card" style="flex:1;min-width:200px;padding:22px;text-align:center;">
          <div style="font-size:30px;font-weight:700;color:#2196f3;">📦</div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Formato: ZIP comprimido</div>
        </div>

        <div class="card" style="flex:1;min-width:200px;padding:22px;text-align:center;">
          <div style="font-size:30px;font-weight:700;color:#ff9800;">🔒</div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Solo administradores</div>
        </div>

      </div>

      <!-- ══ Panel de acciones ══ -->
      <div class="card" style="margin-bottom:26px;">
        <div class="card-header" style="padding:24px 24px 18px;border-bottom:1px solid var(--border,#c8efeb);">
          <h3 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--texto,#1a2e2c);margin:0;">
            Acciones
          </h3>
        </div>
        <div class="card-body" style="padding:24px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">

          <!-- Botón exportar -->
          <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=respaldos&accion=generar">
            <button type="submit"
              style="background:var(--verde,#0aafa0);color:#fff;border:none;border-radius:14px;padding:14px 24px;font-family:'DM Sans',sans-serif;font-weight:700;font-size:15px;cursor:pointer;display:inline-flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(10,175,160,0.18);transition:opacity .2s;">
              <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:18px;height:18px;">
                <path d="M8 1V11M4 7L8 11L12 7"/><path d="M2 14H14"/>
              </svg>
              Generar Respaldo (.zip)
            </button>
          </form>

          <!-- Botón importar -->
          <form method="POST"
                action="<?= BASE_URL ?>/index.php?pagina=respaldos&accion=importar"
                enctype="multipart/form-data"
                id="form-importar">
            <input type="file"
                   name="backup_file"
                   id="backup_file"
                   accept=".zip"
                   style="display:none;"
                   onchange="confirmarImportacion()">
            <label for="backup_file"
              style="background:#e67e22;color:#fff;border-radius:14px;padding:14px 24px;font-family:'DM Sans',sans-serif;font-weight:700;font-size:15px;cursor:pointer;display:inline-flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(230,126,34,0.18);">
              <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:18px;height:18px;">
                <path d="M8 11V1M4 5L8 1L12 5"/><path d="M2 14H14"/>
              </svg>
              Importar Respaldo (.zip)
            </label>
          </form>

          <span style="font-size:12px;color:var(--muted,#5a8a84);max-width:280px;line-height:1.6;">
            La importación reemplazará los datos actuales de la base de datos con el contenido del respaldo.
          </span>

        </div>
      </div>

      <!-- ══ Historial de operaciones ══ -->
      <div class="card">
        <div class="card-header" style="padding:24px 24px 18px;border-bottom:1px solid var(--border,#c8efeb);">
          <h3 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--texto,#1a2e2c);margin:0;">
            Historial de Operaciones
          </h3>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto;">

          <?php if (empty($historial)): ?>
            <div style="padding:40px;text-align:center;color:var(--muted,#5a8a84);">
              <div style="font-size:36px;margin-bottom:12px;">📭</div>
              <p style="font-size:14px;">Aún no hay operaciones registradas. Genera tu primer respaldo para comenzar.</p>
            </div>
          <?php else: ?>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
              <thead>
                <tr style="background:var(--verde,#0aafa0);color:#fff;">
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">#</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Operación</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Formato</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Archivo</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Tamaño</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Usuario</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Fecha y Hora</th>
                  <th style="padding:12px 16px;text-align:left;font-weight:600;">Notas</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($historial as $i => $r): ?>
                  <tr style="border-bottom:1px solid var(--border,#c8efeb);<?= $i % 2 === 0 ? '' : 'background:rgba(10,175,160,0.03);' ?>">
                    <td style="padding:11px 16px;color:var(--muted,#5a8a84);"><?= htmlspecialchars($r['id']) ?></td>

                    <td style="padding:11px 16px;">
                      <?php if ($r['tipo_operacion'] === 'EXPORTACION'): ?>
                        <span style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:700;">
                          EXPORTACIÓN
                        </span>
                      <?php else: ?>
                        <span style="background:#fff3cd;color:#856404;border:1px solid #ffeeba;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:700;">
                          IMPORTACIÓN
                        </span>
                      <?php endif; ?>
                    </td>

                    <td style="padding:11px 16px;">
                      <code style="background:#e9ecef;padding:2px 7px;border-radius:4px;font-size:12px;">
                        <?= htmlspecialchars($r['formato']) ?>
                      </code>
                    </td>

                    <td style="padding:11px 16px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                      <?= htmlspecialchars($r['nombre_archivo']) ?>
                    </td>

                    <td style="padding:11px 16px;" class="bytes-cell" data-bytes="<?= (int)$r['tamanio_bytes'] ?>">
                      <?= number_format((int)$r['tamanio_bytes'] / 1024, 1) ?> KB
                    </td>

                    <td style="padding:11px 16px;">
                      <?= htmlspecialchars($r['nombre_usuario'] ?? '—') ?>
                    </td>

                    <td style="padding:11px 16px;white-space:nowrap;color:var(--muted,#5a8a84);">
                      <?= htmlspecialchars($r['fechayhora']) ?>
                    </td>

                    <td style="padding:11px 16px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($r['observaciones'] ?? '') ?>">
                      <?= htmlspecialchars(mb_strimwidth($r['observaciones'] ?? '—', 0, 50, '…')) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>

        </div>
      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Formatear bytes en la tabla ──────────────────
function formatBytes(bytes, dec = 1) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024, s = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dec)) + ' ' + s[i];
}
document.querySelectorAll('.bytes-cell').forEach(c => {
    const b = parseInt(c.dataset.bytes, 10);
    if (!isNaN(b)) c.textContent = formatBytes(b);
});

// ── Confirmación antes de importar ──────────────
function confirmarImportacion() {
    const input = document.getElementById('backup_file');
    if (!input.files.length) return;

    Swal.fire({
        title: '¿Restaurar base de datos?',
        html: '<p style="color:#555;font-size:14px;">Esta acción <strong>reemplazará todos los datos actuales</strong> con el contenido del archivo ZIP seleccionado.<br><br>Asegúrate de tener un respaldo reciente antes de continuar.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e67e22',
        cancelButtonColor:  '#aaa',
        confirmButtonText:  'Sí, restaurar',
        cancelButtonText:   'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('form-importar').submit();
        } else {
            input.value = '';
        }
    });
}

// ── Alertas desde URL params ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const p = new URLSearchParams(window.location.search);

    if (p.get('exito') === 'exportacion' || p.get('exito') === 'generado') {
        Swal.fire({
            title: '¡Respaldo generado!',
            text:  'El archivo ZIP se ha descargado correctamente.',
            icon:  'success',
            confirmButtonColor: '#0aafa0',
            confirmButtonText:  'Aceptar'
        }).then(() => limpiarUrl());
    }

    if (p.get('exito') === 'importacion') {
        Swal.fire({
            title: '¡Base de datos restaurada!',
            text:  'La importación del respaldo se completó con éxito.',
            icon:  'success',
            confirmButtonColor: '#0aafa0',
            confirmButtonText:  'Aceptar'
        }).then(() => limpiarUrl());
    }

    if (p.get('error')) {
        Swal.fire({
            title: 'Error',
            text:  decodeURIComponent(p.get('error')),
            icon:  'error',
            confirmButtonColor: '#0aafa0'
        }).then(() => limpiarUrl());
    }

    function limpiarUrl() {
        const url = window.location.protocol + '//' + window.location.host + window.location.pathname;
        window.history.replaceState({}, '', url);
    }
});
</script>
