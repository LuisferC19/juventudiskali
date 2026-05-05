<?php
/**
 * views/pages/donadores.php
 * Listado y gestión de donadores.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <div class="card">
        <div class="card-header">
          <h3>Donadores</h3>
          <div class="toolbar">
            <input type="text" class="search-input" placeholder="Buscar donador..." oninput="filtrarTabla(this,'tabla-donadores')">
            <select style="width:120px;">
              <option value="">Todos</option>
              <option>Física</option>
              <option>Moral</option>
            </select>
            <button class="btn btn-primary" onclick="openModal('modal-donador')">+ Nuevo donador</button>
          </div>
        </div>

        <table id="tabla-donadores">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Tipo</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Puntos</th>
              <th>Nivel</th>
              <th>Activo</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#D01</td>
              <td>María García López</td>
              <td>Física</td>
              <td>maria@ejemplo.com</td>
              <td>222-100-0001</td>
              <td>340</td>
              <td><span class="badge badge-amber">Plata</span></td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-reconocimiento')">Reconocimiento</button></td>
            </tr>
            <tr>
              <td>#D02</td>
              <td>Empresa Alfa S.A.</td>
              <td>Moral</td>
              <td>contacto@alfa.com</td>
              <td>222-200-0002</td>
              <td>1,200</td>
              <td><span class="badge badge-blue">Oro</span></td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-reconocimiento')">Reconocimiento</button></td>
            </tr>
            <tr>
              <td>#D03</td>
              <td>Luis Torres Reyes</td>
              <td>Física</td>
              <td>luis@ejemplo.com</td>
              <td>222-300-0003</td>
              <td>85</td>
              <td><span class="badge badge-gray">Bronce</span></td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-reconocimiento')">Reconocimiento</button></td>
            </tr>
            <tr>
              <td>#D04</td>
              <td>Comercial Beta S.C.</td>
              <td>Moral</td>
              <td>info@beta.com</td>
              <td>222-400-0004</td>
              <td>2,350</td>
              <td><span class="badge badge-pink">Diamante</span></td>
              <td><span class="badge badge-green">Sí</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-reconocimiento')">Reconocimiento</button></td>
            </tr>
            <tr>
              <td>#D05</td>
              <td>Ana Ruiz Morales</td>
              <td>Física</td>
              <td>ana@ejemplo.com</td>
              <td>222-500-0005</td>
              <td>45</td>
              <td><span class="badge badge-gray">Bronce</span></td>
              <td><span class="badge badge-red">No</span></td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-reconocimiento')">Reconocimiento</button></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>