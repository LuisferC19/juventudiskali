<?php
/**
 * views/pages/inventario.php
 * Control de inventario de donaciones en especie y monetarias.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- Alerta de stock bajo -->
      <div class="alert alert-warning">
        Stock bajo: <b>Medicamentos</b> — solo 4 cajas disponibles.
      </div>

      <!-- Tabla principal de inventario -->
      <div class="card">
        <div class="card-header">
          <h3>Inventario por tipo de donación</h3>
          <div class="toolbar">
            <button class="btn btn-secondary btn-sm" onclick="openModal('modal-movimiento')">+ Ajuste manual</button>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Tipo donación</th>
              <th>Categoría</th>
              <th>Unidad</th>
              <th>Disponible</th>
              <th>Total recibido</th>
              <th>Total entregado</th>
              <th>Última actualización</th>
              <th>Actualizado por</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Víveres</td>
              <td>Especie</td>
              <td>kg</td>
              <td><b>140</b></td>
              <td>320</td>
              <td>180</td>
              <td>2026-03-19</td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-movimiento')">Movimiento</button></td>
            </tr>
            <tr>
              <td>Ropa</td>
              <td>Especie</td>
              <td>pzas</td>
              <td><b>83</b></td>
              <td>200</td>
              <td>117</td>
              <td>2026-03-18</td>
              <td>Jessica R.</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-movimiento')">Movimiento</button></td>
            </tr>
            <tr>
              <td style="color:var(--danger);font-weight:500;">Medicamentos ⚠</td>
              <td>Especie</td>
              <td>cajas</td>
              <td><b style="color:var(--danger);">4</b></td>
              <td>50</td>
              <td>46</td>
              <td>2026-03-17</td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-movimiento')">Movimiento</button></td>
            </tr>
            <tr>
              <td>Útiles escolares</td>
              <td>Especie</td>
              <td>pzas</td>
              <td><b>62</b></td>
              <td>100</td>
              <td>38</td>
              <td>2026-03-16</td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-movimiento')">Movimiento</button></td>
            </tr>
            <tr>
              <td>Monetaria</td>
              <td>Monetaria</td>
              <td>pesos</td>
              <td><b>$18,400</b></td>
              <td>$84,200</td>
              <td>$65,800</td>
              <td>2026-03-19</td>
              <td>Eva Sánchez</td>
              <td><button class="btn btn-secondary btn-sm" onclick="openModal('modal-movimiento')">Movimiento</button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Movimientos recientes -->
      <div class="card">
        <div class="card-header">
          <h3>Movimientos recientes</h3>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Inventario</th>
              <th>Tipo movimiento</th>
              <th>Cantidad</th>
              <th>Stock anterior</th>
              <th>Stock posterior</th>
              <th>Tipo referencia</th>
              <th>Motivo</th>
              <th>Usuario</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#M001</td>
              <td>Víveres</td>
              <td><span class="badge badge-green">Entrada</span></td>
              <td>20 kg</td>
              <td>120 kg</td>
              <td>140 kg</td>
              <td>Donación</td>
              <td>—</td>
              <td>Eva Sánchez</td>
              <td>2026-03-19</td>
            </tr>
            <tr>
              <td>#M002</td>
              <td>Medicamentos</td>
              <td><span class="badge badge-red">Salida</span></td>
              <td>1 caja</td>
              <td>5 cajas</td>
              <td>4 cajas</td>
              <td>Entrega</td>
              <td>—</td>
              <td>Jessica R.</td>
              <td>2026-03-17</td>
            </tr>
            <tr>
              <td>#M003</td>
              <td>Ropa</td>
              <td><span class="badge badge-amber">Ajuste</span></td>
              <td>5 pzas</td>
              <td>78 pzas</td>
              <td>83 pzas</td>
              <td>Ajuste manual</td>
              <td>Conteo físico</td>
              <td>Eva Sánchez</td>
              <td>2026-03-18</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>