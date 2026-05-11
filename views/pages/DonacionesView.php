<?php
/**
 * views/pages/DonacionesView.php
 * Registro y gestión de donaciones recibidas.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- KPIs de donaciones -->
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Total recibido</div>
          <div class="kpi-value">$84,200</div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Pendientes</div>
          <div class="kpi-value">14</div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">Asignadas</div>
          <div class="kpi-value">38</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Entregadas</div>
          <div class="kpi-value">579</div>
        </div>
      </div>

      <!-- Tabla de donaciones -->
      <div class="card">
        <div class="card-header">
          <h3>Donaciones</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar..." oninput="filtrarTabla(this,'tabla-donaciones')">
            <select style="width:120px;">
              <option value="">Todos</option>
              <option>Pendiente</option>
              <option>Asignada</option>
              <option>Entregada</option>
            </select>
            <button class="btn btn-primary" onclick="openModal('modal-donacion')">+ Nueva donación</button>
          </div>
        </div>

        <!-- Tabs de filtrado visual -->
        <div class="tabs">
          <div class="tab active" onclick="activarTab(this)">Todas</div>
          <div class="tab" onclick="activarTab(this)">Pendientes</div>
          <div class="tab" onclick="activarTab(this)">Asignadas</div>
          <div class="tab" onclick="activarTab(this)">Entregadas</div>
          <div class="tab" onclick="activarTab(this)">Monetarias</div>
          <div class="tab" onclick="activarTab(this)">En especie</div>
        </div>

        <table id="tabla-donaciones">
          <thead>
            <tr>
              <th>ID</th>
              <th>Donador</th>
              <th>Tipo</th>
              <th>Cantidad</th>
              <th>Fecha recepción</th>
              <th>Campaña</th>
              <th>Registrado por</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#R001</td>
              <td>María García</td>
              <td>Víveres</td>
              <td>20 kg</td>
              <td>2026-03-15</td>
              <td>Víveres Marzo</td>
              <td>Eva Sánchez</td>
              <td><span class="badge badge-green">Entregada</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de donación #R001')">Ver</button></td>
            </tr>
            <tr>
              <td>#R002</td>
              <td>Empresa Alfa</td>
              <td>Monetaria</td>
              <td>$5,000</td>
              <td>2026-03-16</td>
              <td>Útiles</td>
              <td>Eva Sánchez</td>
              <td><span class="badge badge-amber">Pendiente</span></td>
              <td><button class="btn btn-primary btn-sm" onclick="openModal('modal-entrega')">Asignar</button></td>
            </tr>
            <tr>
              <td>#R003</td>
              <td>Luis Torres</td>
              <td>Ropa</td>
              <td>15 pzas</td>
              <td>2026-03-17</td>
              <td>Invierno 2026</td>
              <td>Jessica R.</td>
              <td><span class="badge badge-blue">Asignada</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de donación #R003')">Ver</button></td>
            </tr>
            <tr>
              <td>#R004</td>
              <td>Comercial Beta</td>
              <td>Monetaria</td>
              <td>$10,000</td>
              <td>2026-03-10</td>
              <td>Invierno 2026</td>
              <td>Eva Sánchez</td>
              <td><span class="badge badge-green">Entregada</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de donación #R004')">Ver</button></td>
            </tr>
            <tr>
              <td>#R005</td>
              <td>Ana Ruiz</td>
              <td>Medicamentos</td>
              <td>3 cajas</td>
              <td>2026-03-18</td>
              <td>Medicamentos Urgentes</td>
              <td>Jessica R.</td>
              <td><span class="badge badge-amber">Pendiente</span></td>
              <td><button class="btn btn-primary btn-sm" onclick="openModal('modal-entrega')">Asignar</button></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>