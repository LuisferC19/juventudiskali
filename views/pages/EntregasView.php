<?php
/**
 * views/pages/entregas.php
 * Control de entregas de donaciones a beneficiarios.
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
          <div class="kpi-label">Total entregas</div>
          <div class="kpi-value">631</div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Programadas</div>
          <div class="kpi-value">12</div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">En camino</div>
          <div class="kpi-value">5</div>
        </div>
      </div>

      <!-- Tabla de entregas -->
      <div class="card">
        <div class="card-header">
          <h3>Entregas</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar entrega..." oninput="filtrarTabla(this,'tabla-entregas')">
            <select style="width:120px;">
              <option value="">Todos</option>
              <option>Programada</option>
              <option>En camino</option>
              <option>Completada</option>
              <option>Cancelada</option>
            </select>
            <button class="btn btn-primary" onclick="openModal('modal-entrega')">+ Nueva entrega</button>
          </div>
        </div>

        <table id="tabla-entregas">
          <thead>
            <tr>
              <th>ID</th>
              <th>Donación</th>
              <th>Beneficiario</th>
              <th>Cantidad</th>
              <th>Fecha entrega</th>
              <th>Responsable</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#E001</td>
              <td>Víveres 20kg</td>
              <td>Ana M. Pérez</td>
              <td>5 kg</td>
              <td>2026-03-15 10:00</td>
              <td>Jessica R.</td>
              <td><span class="badge badge-green">Completada</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-asignacion')">Asignar voluntario</button></td>
            </tr>
            <tr>
              <td>#E002</td>
              <td>Medicamentos</td>
              <td>José R. Cruz</td>
              <td>1 caja</td>
              <td>2026-03-20 09:00</td>
              <td>Jessica R.</td>
              <td><span class="badge badge-amber">Programada</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-asignacion')">Asignar voluntario</button></td>
            </tr>
            <tr>
              <td>#E003</td>
              <td>Ropa 15 pzas</td>
              <td>María F. Soto</td>
              <td>3 pzas</td>
              <td>2026-03-19 14:00</td>
              <td>Jessica R.</td>
              <td><span class="badge badge-blue">En camino</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="showToast('Módulo GPS — ver seguimiento')">Ver GPS</button></td>
            </tr>
            <tr>
              <td>#E004</td>
              <td>Útiles escolares</td>
              <td>Pedro A. Leal</td>
              <td>1 kit</td>
              <td>2026-03-22 11:00</td>
              <td>Eva Sánchez</td>
              <td><span class="badge badge-amber">Programada</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-asignacion')">Asignar voluntario</button></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
