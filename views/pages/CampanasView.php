<?php
/**
 * views/pages/CampanasView.php
 * Gestión de campañas del sistema Iskalli.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- KPIs de campañas -->
      <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="kpi">
          <div class="kpi-label">Total</div>
          <div class="kpi-value">12</div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Activas</div>
          <div class="kpi-value">4</div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">Cerradas</div>
          <div class="kpi-value">8</div>
        </div>
      </div>

      <!-- Tabla de campañas -->
      <div class="card">
        <div class="card-header">
          <h3>Campañas</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar campaña..." oninput="filtrarTabla(this,'tabla-campanas')">
            <select style="width:130px;" onchange="filtrarTabla(this,'tabla-campanas')">
              <option value="">Todos los estados</option>
              <option>Activa</option>
              <option>Planificada</option>
              <option>Cerrada</option>
            </select>
            <button class="btn btn-primary" onclick="openModal('modal-campana')">+ Nueva campaña</button>
          </div>
        </div>

        <table id="tabla-campanas">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Tipo meta</th>
              <th>Meta económica</th>
              <th>Inicio</th>
              <th>Cierre</th>
              <th>Estado</th>
              <th>Creada por</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#C001</td>
              <td>Invierno 2026</td>
              <td>Ambas</td>
              <td>$50,000</td>
              <td>2026-01-10</td>
              <td>2026-03-31</td>
              <td><span class="badge badge-green">Activa</span></td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-avance')">+ Avance</button></td>
            </tr>
            <tr>
              <td>#C002</td>
              <td>Víveres Marzo</td>
              <td>Material</td>
              <td>—</td>
              <td>2026-03-01</td>
              <td>2026-03-31</td>
              <td><span class="badge badge-green">Activa</span></td>
              <td>Fernando R.</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-avance')">+ Avance</button></td>
            </tr>
            <tr>
              <td>#C003</td>
              <td>Útiles Escolares</td>
              <td>Económica</td>
              <td>$20,000</td>
              <td>2026-02-01</td>
              <td>2026-04-30</td>
              <td><span class="badge badge-amber">Planificada</span></td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-avance')">+ Avance</button></td>
            </tr>
            <tr>
              <td>#C004</td>
              <td>Medicamentos Urgentes</td>
              <td>Material</td>
              <td>—</td>
              <td>2026-03-10</td>
              <td>2026-04-10</td>
              <td><span class="badge badge-green">Activa</span></td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-avance')">+ Avance</button></td>
            </tr>
            <tr>
              <td>#C005</td>
              <td>Ropa Otoño 2025</td>
              <td>Material</td>
              <td>—</td>
              <td>2025-09-01</td>
              <td>2025-11-30</td>
              <td><span class="badge badge-gray">Cerrada</span></td>
              <td>Fernando R.</td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Campaña cerrada — no se puede editar')">Ver detalle</button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Avances registrados -->
      <div class="card">
        <div class="card-header">
          <h3>Avances recientes</h3>
          <button class="btn btn-primary" onclick="openModal('modal-avance')">+ Registrar avance</button>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Campaña</th>
              <th>Avance %</th>
              <th>Monto recaudado</th>
              <th>Descripción</th>
              <th>Reportado por</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#AV001</td>
              <td>Invierno 2026</td>
              <td><b style="color:var(--accent);">72%</b></td>
              <td>$36,000</td>
              <td>Superamos el 70% de la meta económica</td>
              <td>Eva Sánchez</td>
              <td>2026-03-18</td>
            </tr>
            <tr>
              <td>#AV002</td>
              <td>Víveres Marzo</td>
              <td><b style="color:var(--accent);">45%</b></td>
              <td>—</td>
              <td>Se han distribuido 144 kg de víveres</td>
              <td>Fernando R.</td>
              <td>2026-03-17</td>
            </tr>
            <tr>
              <td>#AV003</td>
              <td>Útiles Escolares</td>
              <td><b style="color:var(--amber);">18%</b></td>
              <td>$3,600</td>
              <td>Inicio de recolección en escuelas</td>
              <td>Eva Sánchez</td>
              <td>2026-03-15</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div>

<?php require_once 'views/layouts/footer.php'; ?>