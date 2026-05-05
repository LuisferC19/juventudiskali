<?php
/**
 * views/pages/dashboard.php
 * Dashboard principal con KPIs, acciones rápidas y actividad reciente.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">

  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- ══════════ KPIs ══════════ -->
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Total donado (MXN)</div>
          <div class="kpi-value">$84,200</div>
          <div class="kpi-sub">↑ 12% este mes</div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Beneficiarios</div>
          <div class="kpi-value">248</div>
          <div class="kpi-sub">↑ 5 nuevos</div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">Campañas activas</div>
          <div class="kpi-value">4</div>
          <div class="kpi-sub">de 12 totales</div>
        </div>
        <div class="kpi danger">
          <div class="kpi-label">Entregas realizadas</div>
          <div class="kpi-value">631</div>
          <div class="kpi-sub">este periodo</div>
        </div>
      </div>

      <!-- ══════════ Acciones rápidas ══════════ -->
      <div class="quick-actions">
        <div class="qa-btn" onclick="openModal('modal-donacion')">
          <div class="qa-icon">💰</div>
          <div class="qa-label">Nueva donación</div>
        </div>
        <div class="qa-btn" onclick="openModal('modal-beneficiario')">
          <div class="qa-icon">👤</div>
          <div class="qa-label">Nuevo beneficiario</div>
        </div>
        <div class="qa-btn" onclick="openModal('modal-entrega')">
          <div class="qa-icon">📦</div>
          <div class="qa-label">Nueva entrega</div>
        </div>
        <div class="qa-btn" onclick="openModal('modal-campana')">
          <div class="qa-icon">🚀</div>
          <div class="qa-label">Nueva campaña</div>
        </div>
        <div class="qa-btn" onclick="showToast('Abre la sección de Quejas desde el menú lateral')">
          <div class="qa-icon">💬</div>
          <div class="qa-label">Nueva queja/sug.</div>
        </div>
      </div>

      <!-- ══════════ Dos columnas ══════════ -->
      <div class="two-col">

        <!-- Campañas activas con barras de progreso -->
        <div class="card">
          <div class="card-header">
            <h3>Campañas activas</h3>
          </div>
          <div class="progress-wrap">
            <div class="prog-item">
              <div class="prog-label">
                <span>Invierno 2026</span>
                <span style="color:var(--accent);font-weight:500;">72%</span>
              </div>
              <div class="prog-bar"><div class="prog-fill" style="width:72%"></div></div>
            </div>
            <div class="prog-item">
              <div class="prog-label">
                <span>Víveres Marzo</span>
                <span style="color:var(--accent);font-weight:500;">45%</span>
              </div>
              <div class="prog-bar"><div class="prog-fill" style="width:45%"></div></div>
            </div>
            <div class="prog-item">
              <div class="prog-label">
                <span>Útiles Escolares</span>
                <span style="color:var(--amber);font-weight:500;">18%</span>
              </div>
              <div class="prog-bar"><div class="prog-fill" style="width:18%;background:var(--amber)"></div></div>
            </div>
            <div class="prog-item">
              <div class="prog-label">
                <span>Medicamentos</span>
                <span style="color:var(--danger);font-weight:500;">8%</span>
              </div>
              <div class="prog-bar"><div class="prog-fill" style="width:8%;background:var(--danger)"></div></div>
            </div>
          </div>
        </div>

        <!-- Actividad reciente -->
        <div class="card">
          <div class="card-header"><h3>Actividad reciente</h3></div>
          <div class="activity-item">
            <div class="activity-dot" style="background:#1A7A5E;"></div>
            <div class="activity-text">Donación registrada — Víveres 20 kg</div>
            <div class="activity-time">5 min</div>
          </div>
          <div class="activity-item">
            <div class="activity-dot" style="background:#3B6FD4;"></div>
            <div class="activity-text">Entrega completada — Beneficiario #142</div>
            <div class="activity-time">1 h</div>
          </div>
          <div class="activity-item">
            <div class="activity-dot" style="background:#1A7A5E;"></div>
            <div class="activity-text">Nuevo beneficiario registrado</div>
            <div class="activity-time">2 h</div>
          </div>
          <div class="activity-item">
            <div class="activity-dot" style="background:#C68B0A;"></div>
            <div class="activity-text">Stock bajo — Medicamentos: 4 cajas</div>
            <div class="activity-time">3 h</div>
          </div>
          <div class="activity-item">
            <div class="activity-dot" style="background:#993556;"></div>
            <div class="activity-text">Nueva queja recibida — Beneficiario</div>
            <div class="activity-time">5 h</div>
          </div>
        </div>

      </div><!-- /two-col -->

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<?php require_once 'views/layouts/footer.php'; ?>