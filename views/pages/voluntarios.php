<?php
/**
 * views/pages/voluntarios.php
 * Gestión de voluntarios y sus asignaciones.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- Tabla de voluntarios -->
      <div class="card">
        <div class="card-header">
          <h3>Voluntarios</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar voluntario..." oninput="filtrarTabla(this,'tabla-voluntarios')">
            <select style="width:150px;">
              <option value="">Todas las disponibilidades</option>
              <option>Tiempo completo</option>
              <option>Lunes–viernes</option>
              <option>Fines de semana</option>
              <option>Bajo demanda</option>
            </select>
            <button class="btn btn-primary" onclick="openModal('modal-voluntario')">+ Nuevo voluntario</button>
          </div>
        </div>

        <table id="tabla-voluntarios">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Zona asignada</th>
              <th>Disponibilidad</th>
              <th>Fecha ingreso</th>
              <th>Activo</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#V001</td>
              <td>Jessica R.</td>
              <td>222-500-0001</td>
              <td>San Marcos</td>
              <td><span class="badge badge-blue">Tiempo completo</span></td>
              <td>2025-06-01</td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-asignacion')">Ver asignaciones</button></td>
            </tr>
            <tr>
              <td>#V002</td>
              <td>Carlos M.</td>
              <td>222-500-0002</td>
              <td>La Loma</td>
              <td><span class="badge badge-gray">Fines de semana</span></td>
              <td>2025-09-15</td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-asignacion')">Ver asignaciones</button></td>
            </tr>
            <tr>
              <td>#V003</td>
              <td>Fernando R.</td>
              <td>222-500-0003</td>
              <td>El Rincón</td>
              <td><span class="badge badge-amber">Lunes–viernes</span></td>
              <td>2025-11-01</td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-asignacion')">Ver asignaciones</button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Asignaciones de voluntarios -->
      <div class="card">
        <div class="card-header">
          <h3>Asignaciones de voluntarios</h3>
          <button class="btn btn-primary" onclick="openModal('modal-asignacion')">+ Nueva asignación</button>
        </div>

        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Voluntario</th>
              <th>Entrega</th>
              <th>Asignado por</th>
              <th>Fecha asignación</th>
              <th>Fecha compromiso</th>
              <th>Estado</th>
              <th>Observaciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#A001</td>
              <td>Jessica R.</td>
              <td>#E002 — Medicamentos</td>
              <td>Eva Sánchez</td>
              <td>2026-03-18 08:00</td>
              <td>2026-03-20 09:00</td>
              <td><span class="badge badge-amber">Pendiente</span></td>
              <td>—</td>
            </tr>
            <tr>
              <td>#A002</td>
              <td>Jessica R.</td>
              <td>#E003 — Ropa 3 pzas</td>
              <td>Eva Sánchez</td>
              <td>2026-03-19 07:30</td>
              <td>2026-03-19 14:00</td>
              <td><span class="badge badge-blue">En camino</span></td>
              <td>Salió puntual</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>