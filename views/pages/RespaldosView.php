<?php
/**
 * views/pages/RespaldosView.php
 * Vista para el módulo de respaldos de base de datos.
 */

// Incluir layouts
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content" style="padding:32px; background: var(--fondo, #f8fffe); min-height: calc(100vh - 72px);">

      <!-- ══ Encabezado ══ -->
      <div style="margin-bottom:28px;">
        <h2 style="font-family:'Syne',sans-serif;font-size:24px;font-weight:700;letter-spacing:-0.5px;color:var(--texto,#1a2e2c);margin-bottom:8px;">
          Respaldos de Base de Datos 💾
        </h2>
        <p style="font-size:14px;color:var(--muted,#5a8a84);max-width:620px;line-height:1.7;">
          Genera y descarga respaldos completos del sistema ISKALLI de forma segura y profesional.
        </p>
      </div>

      <!-- ══ Tarjetas de información ══ -->
      <div style="display:flex;gap:20px;margin-bottom:28px;flex-wrap:wrap;">

        <div class="card" style="flex:1;min-width:210px;padding:22px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:var(--verde,#0aafa0);">📊
            <?php
            $backupDir = __DIR__ . '/../../Respaldos';
            $files = glob($backupDir . '/*.sql');
            echo count($files);
            ?>
          </div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Respaldos disponibles</div>
        </div>

        <div class="card" style="flex:1;min-width:210px;padding:22px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#4caf50;">🗄️</div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Base de datos: iskali</div>
        </div>

        <div class="card" style="flex:1;min-width:210px;padding:22px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#2196f3;">⚡</div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Generación automática</div>
        </div>

        <div class="card" style="flex:1;min-width:210px;padding:22px;text-align:center;">
          <div style="font-size:32px;font-weight:700;color:#ff9800;">🔒</div>
          <div style="font-size:13px;color:var(--muted,#5a8a84);margin-top:8px;">Solo administradores</div>
        </div>

      </div>

      <!-- ══ Mensajes ══ -->
      <?php if (!empty($error)): ?>
        <div style="background:rgba(244,67,54,0.08);border:1px solid rgba(244,67,54,0.18);border-radius:16px;padding:18px;margin-bottom:24px;display:flex;align-items:flex-start;gap:14px;">
          <svg viewBox="0 0 16 16" fill="none" stroke="#d32f2f" stroke-width="1.5" style="width:20px;height:20px;flex-shrink:0;margin-top:2px;">
            <circle cx="8" cy="8" r="6"/>
            <path d="M8 4V8M8 10V12"/>
          </svg>
          <div>
            <div style="font-weight:700;color:#d32f2f;margin-bottom:4px;">Error</div>
            <div style="color:var(--texto,#1a2e2c);font-size:14px;line-height:1.6;"> <?= htmlspecialchars($error) ?></div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($mensaje)): ?>
        <div style="background:rgba(10,175,160,0.12);border:1px solid rgba(10,175,160,0.25);border-radius:16px;padding:18px;margin-bottom:24px;display:flex;align-items:flex-start;gap:14px;">
          <svg viewBox="0 0 16 16" fill="none" stroke="#0aafa0" stroke-width="1.5" style="width:20px;height:20px;flex-shrink:0;margin-top:2px;">
            <path d="M2 8L6 12L14 4"/>
          </svg>
          <div>
            <div style="font-weight:700;color:var(--verde,#0aafa0);margin-bottom:4px;">Éxito</div>
            <div style="color:var(--texto,#1a2e2c);font-size:14px;line-height:1.6;"> <?= htmlspecialchars($mensaje) ?></div>
          </div>
        </div>
      <?php endif; ?>

      <!-- ══ Panel principal ══ -->
      <div class="card" style="margin-bottom:26px;">
        <div class="card-header" style="padding:24px 24px 18px;border-bottom:1px solid var(--border,#c8efeb);">
          <h3 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--texto,#1a2e2c);margin:0;">
            Generar Respaldo
          </h3>
        </div>
        <div class="card-body" style="padding:24px 24px 28px;">

          <div style="display:grid;grid-template-columns:1fr auto;gap:24px;align-items:start;">

            <div>
              <p style="color:var(--muted,#5a8a84);font-size:14px;line-height:1.75;margin-bottom:18px;">
                Crea un respaldo completo de la base de datos con todas las tablas, datos y objetos. El archivo se generará y descargará automáticamente en tu navegador.
              </p>

              <div style="background:var(--card-bg,#ffffff);border:1px solid var(--border,#c8efeb);border-radius:18px;padding:20px;">
                <h4 style="font-size:15px;font-weight:700;color:var(--texto,#1a2e2c);margin-bottom:14px;">Detalles del respaldo:</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;font-size:13px;line-height:1.6;color:var(--texto,#1a2e2c);">
                  <div><span style="font-weight:700;color:var(--verde,#0aafa0);">Base de datos:</span> iskali</div>
                  <div><span style="font-weight:700;color:var(--verde,#0aafa0);">Formato:</span> SQL completo</div>
                  <div><span style="font-weight:700;color:var(--verde,#0aafa0);">Incluye:</span> Tablas, datos, rutinas, triggers</div>
                  <div><span style="font-weight:700;color:var(--verde,#0aafa0);">Nombre:</span> backup_iskali_YYYY-MM-DD_HH-MM-SS.sql</div>
                </div>
              </div>
            </div>

            <div style="text-align:center;">
              <form method="POST" action="<?= BASE_URL ?>/index.php?pagina=respaldos&accion=generar" style="display:inline;">
                <button type="submit" style="background:var(--verde,#0aafa0);color:white;border:none;border-radius:16px;padding:18px 28px;font-family:'DM Sans',sans-serif;font-weight:700;font-size:16px;cursor:pointer;display:inline-flex;align-items:center;gap:10px;transition:all 0.2s ease;margin:0 auto;box-shadow:0 20px 40px rgba(10,175,160,0.16);">
                  <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:20px;height:20px;">
                    <path d="M2 4H14V12H2V4Z"/>
                    <path d="M6 8H10"/>
                    <path d="M8 6V10"/>
                  </svg>
                  Generar Respaldo
                </button>
              </form>
              <p style="font-size:13px;color:var(--muted,#5a8a84);margin-top:12px;max-width:240px;margin-left:auto;margin-right:auto;line-height:1.6;">
                El proceso puede tardar unos segundos según el tamaño de la base de datos.
              </p>
            </div>

          </div>

        </div>
      </div>

      <!-- ══ Información adicional ══ -->
      <div class="card">
        <div class="card-header" style="padding:24px 24px 18px;border-bottom:1px solid var(--border,#c8efeb);">
          <h3 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--texto,#1a2e2c);margin:0;">
            Seguridad y uso
          </h3>
        </div>
        <div class="card-body" style="padding:24px;">
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <div style="display:flex;gap:14px;">
              <div style="width:44px;height:44px;background:rgba(10,175,160,0.12);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 16 16" fill="none" stroke="#0aafa0" stroke-width="1.5" style="width:22px;height:22px;">
                  <rect x="1" y="1" width="14" height="14" rx="2"/>
                  <path d="M4 8L7 11L12 4"/>
                </svg>
              </div>
              <div>
                <div style="font-weight:700;color:var(--texto,#1a2e2c);margin-bottom:4px;">Acceso restringido</div>
                <div style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;">Solo usuarios Administrador pueden generar respaldos.</div>
              </div>
            </div>

            <div style="display:flex;gap:14px;">
              <div style="width:44px;height:44px;background:rgba(10,175,160,0.12);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 16 16" fill="none" stroke="#0aafa0" stroke-width="1.5" style="width:22px;height:22px;">
                  <path d="M8 1V15M1 8H15" stroke-linecap="round"/>
                </svg>
              </div>
              <div>
                <div style="font-weight:700;color:var(--texto,#1a2e2c);margin-bottom:4px;">Archivos temporales</div>
                <div style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;">Los respaldos se eliminan automáticamente tras la descarga.</div>
              </div>
            </div>

            <div style="display:flex;gap:14px;">
              <div style="width:44px;height:44px;background:rgba(10,175,160,0.12);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 16 16" fill="none" stroke="#0aafa0" stroke-width="1.5" style="width:22px;height:22px;">
                  <path d="M8 2C9.1 2 10 2.9 10 4S9.1 6 8 6 6 5.1 6 4 6.9 2 8 2ZM14 13C14 10.2 11.3 9 8 9S2 10.2 2 13"/>
                </svg>
              </div>
              <div>
                <div style="font-weight:700;color:var(--texto,#1a2e2c);margin-bottom:4px;">Auditoría completa</div>
                <div style="font-size:13px;color:var(--muted,#5a8a84);line-height:1.6;">Incluye todas las tablas y relaciones del sistema.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
