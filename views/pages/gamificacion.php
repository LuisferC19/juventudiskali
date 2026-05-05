<?php
/**
 * views/pages/gamificacion.php
 * Sistema de gamificación: niveles, insignias y reconocimientos.
 */
require_once 'views/layouts/header.php';
?>

<div style="display:flex;width:100%;">
  <?php require_once 'views/layouts/sidebar.php'; ?>

  <div class="main">
    <?php require_once 'views/layouts/topbar.php'; ?>

    <div class="content">

      <!-- KPIs de gamificación -->
      <div class="kpi-grid">
        <div class="kpi">
          <div class="kpi-label">Niveles definidos</div>
          <div class="kpi-value">4</div>
        </div>
        <div class="kpi amber">
          <div class="kpi-label">Insignias activas</div>
          <div class="kpi-value">8</div>
        </div>
        <div class="kpi blue">
          <div class="kpi-label">Reconocimientos emitidos</div>
          <div class="kpi-value">23</div>
        </div>
        <div class="kpi pink">
          <div class="kpi-label">Donadores con nivel</div>
          <div class="kpi-value">86</div>
        </div>
      </div>

      <!-- Dos columnas: Niveles + Insignias -->
      <div class="two-col">

        <!-- Tabla de niveles -->
        <div class="card">
          <div class="card-header">
            <h3>Niveles de gamificación</h3>
            <button class="btn btn-primary" onclick="showToast('Módulo de niveles — próximamente')">+ Nuevo nivel</button>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Pts mínimos</th>
                <th>Pts máximos</th>
                <th>Descripción</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#N1</td>
                <td><span class="badge badge-gray">Bronce</span></td>
                <td>0</td>
                <td>99</td>
                <td>Primer nivel</td>
              </tr>
              <tr>
                <td>#N2</td>
                <td><span class="badge badge-amber">Plata</span></td>
                <td>100</td>
                <td>499</td>
                <td>Donador frecuente</td>
              </tr>
              <tr>
                <td>#N3</td>
                <td><span class="badge badge-blue">Oro</span></td>
                <td>500</td>
                <td>999</td>
                <td>Donador destacado</td>
              </tr>
              <tr>
                <td>#N4</td>
                <td><span class="badge badge-pink">Diamante</span></td>
                <td>1000</td>
                <td>—</td>
                <td>Donador élite</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Grid de insignias -->
        <div class="card">
          <div class="card-header">
            <h3>Insignias</h3>
            <button class="btn btn-primary" onclick="showToast('Módulo de insignias — próximamente')">+ Nueva insignia</button>
          </div>
          <div class="insignia-grid">
            <div class="ins-card">
              <div class="ins-icon">🌱</div>
              <div class="ins-name">Primera donación</div>
              <div class="ins-pts">+10 pts</div>
              <div class="ins-status"><span class="badge badge-green">Activa</span></div>
            </div>
            <div class="ins-card">
              <div class="ins-icon">🔥</div>
              <div class="ins-name">Donador frecuente</div>
              <div class="ins-pts">+50 pts</div>
              <div class="ins-status"><span class="badge badge-green">Activa</span></div>
            </div>
            <div class="ins-card">
              <div class="ins-icon">⭐</div>
              <div class="ins-name">Donador destacado</div>
              <div class="ins-pts">+100 pts</div>
              <div class="ins-status"><span class="badge badge-green">Activa</span></div>
            </div>
            <div class="ins-card">
              <div class="ins-icon">💎</div>
              <div class="ins-name">Gran contribuidor</div>
              <div class="ins-pts">+200 pts</div>
              <div class="ins-status"><span class="badge badge-green">Activa</span></div>
            </div>
            <div class="ins-card">
              <div class="ins-icon">🏅</div>
              <div class="ins-name">Campaña completada</div>
              <div class="ins-pts">+75 pts</div>
              <div class="ins-status"><span class="badge badge-green">Activa</span></div>
            </div>
            <div class="ins-card" onclick="openModal('modal-reconocimiento')" style="cursor:pointer;">
              <div class="ins-icon">+</div>
              <div class="ins-name">Asignar insignia</div>
              <div class="ins-pts">a donador</div>
            </div>
          </div>
        </div>

      </div><!-- /two-col -->

      <!-- Reconocimientos emitidos -->
      <div class="card">
        <div class="card-header">
          <h3>Reconocimientos emitidos</h3>
          <button class="btn btn-primary" onclick="openModal('modal-reconocimiento')">+ Nuevo reconocimiento</button>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Donador</th>
              <th>Campaña</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>PDF</th>
              <th>Fecha emisión</th>
              <th>Emitido por</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#RC001</td>
              <td>María García</td>
              <td>Invierno 2026</td>
              <td><span class="badge badge-blue">Participación</span></td>
              <td>Reconocimiento por apoyo en campaña</td>
              <td><a href="#" style="color:var(--accent);font-size:11px;" onclick="showToast('Descarga de PDF simulada'); return false;">Descargar</a></td>
              <td>2026-03-10</td>
              <td>Eva Sánchez</td>
            </tr>
            <tr>
              <td>#RC002</td>
              <td>Empresa Alfa</td>
              <td>—</td>
              <td><span class="badge badge-amber">Destacado</span></td>
              <td>Donador más activo del trimestre</td>
              <td><a href="#" style="color:var(--accent);font-size:11px;" onclick="showToast('Descarga de PDF simulada'); return false;">Descargar</a></td>
              <td>2026-03-15</td>
              <td>Eva Sánchez</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>