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

      <!-- KPIs -->
      <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="kpi">
          <div class="kpi-label">Total</div>
          <div class="kpi-value">248</div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Activos</div>
          <div class="kpi-value">210</div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">En revisión</div>
          <div class="kpi-value">38</div>
        </div>
      </div>

      <!-- Tabla de beneficiarios -->
      <div class="card">
        <div class="card-header">
          <h3>Beneficiarios</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar beneficiario..." oninput="filtrarTabla(this,'tabla-beneficiarios')">
            <select style="width:120px;">
              <option value="">Todos</option>
              <option>Activo</option>
              <option>En revisión</option>
              <option>Inactivo</option>
            </select>
            <button class="btn btn-primary" onclick="openModal('modal-beneficiario')">+ Registrar</button>
          </div>
        </div>

        <table id="tabla-beneficiarios">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre completo</th>
              <th>Edad</th>
              <th>Comunidad</th>
              <th>Teléfono</th>
              <th>Tipo apoyo</th>
              <th>Estado</th>
              <th>Registrado por</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#B001</td>
              <td>Ana M. Pérez Ruiz</td>
              <td>34</td>
              <td>San Marcos</td>
              <td>222-111-0001</td>
              <td>Víveres</td>
              <td><span class="badge badge-green">Activo</span></td>
              <td>Jessica R.</td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de #B001 — Ana M. Pérez')">Ver</button></td>
            </tr>
            <tr>
              <td>#B002</td>
              <td>José R. Cruz Hdz.</td>
              <td>67</td>
              <td>La Loma</td>
              <td>222-111-0002</td>
              <td>Medicamentos</td>
              <td><span class="badge badge-green">Activo</span></td>
              <td>Jessica R.</td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de #B002 — José R. Cruz')">Ver</button></td>
            </tr>
            <tr>
              <td>#B003</td>
              <td>María F. Soto Gzz.</td>
              <td>12</td>
              <td>El Rincón</td>
              <td>222-111-0003</td>
              <td>Útiles</td>
              <td><span class="badge badge-amber">Revisión</span></td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de #B003 — María F. Soto')">Ver</button></td>
            </tr>
            <tr>
              <td>#B004</td>
              <td>Pedro A. Leal Gómez</td>
              <td>52</td>
              <td>San Marcos</td>
              <td>222-111-0004</td>
              <td>Víveres</td>
              <td><span class="badge badge-green">Activo</span></td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de #B004 — Pedro A. Leal')">Ver</button></td>
            </tr>
            <tr>
              <td>#B005</td>
              <td>Rosa E. Martín Hdz.</td>
              <td>28</td>
              <td>La Loma</td>
              <td>222-111-0005</td>
              <td>Múltiple</td>
              <td><span class="badge badge-green">Activo</span></td>
              <td>Jessica R.</td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Detalle de #B005 — Rosa E. Martín')">Ver</button></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>