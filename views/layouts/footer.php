<?php
/**
 * views/layouts/footer.php
 * Cierra el HTML, incluye modales globales, toast y el JS principal.
 */
?>

<!-- ══════════════════════════════════════════
     MODALES GLOBALES (disponibles en todas las páginas)
     ══════════════════════════════════════════ -->

<!-- Modal: Nueva donación -->
<div class="modal-overlay" id="modal-donacion">
  <div class="modal">
    <div class="modal-header">
      <h3>Nueva donación</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-donacion')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group"><label>Donador *</label><select><option>— Seleccionar —</option><option>#D01 · María García López</option><option>#D02 · Empresa Alfa S.A.</option><option>#D03 · Luis Torres Reyes</option></select></div>
        <div class="form-group"><label>Tipo de donación *</label><select><option>— Seleccionar —</option><option>Monetaria</option><option>Víveres</option><option>Ropa</option><option>Medicamentos</option><option>Útiles escolares</option></select></div>
        <div class="form-group"><label>Cantidad *</label><input type="number" placeholder="0" min="1"></div>
        <div class="form-group"><label>Unidad</label><select><option>kg</option><option>pzas</option><option>pesos</option><option>cajas</option><option>litros</option></select></div>
        <div class="form-group"><label>Fecha de recepción *</label><input type="date"></div>
        <div class="form-group"><label>Campaña asociada</label><select><option>— Sin campaña —</option><option>Invierno 2026</option><option>Víveres Marzo</option><option>Útiles Escolares</option></select></div>
        <div class="form-group"><label>Registrado por *</label><select><option>Eva Sánchez</option><option>Jessica R.</option></select></div>
        <div class="form-group"><label>Estado</label><select><option>Pendiente</option><option>Asignada</option><option>Entregada</option></select></div>
        <div class="form-group full"><label>Notas adicionales</label><textarea rows="2" placeholder="Observaciones..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-donacion')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-donacion','donación')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Registrar beneficiario -->
<div class="modal-overlay" id="modal-beneficiario">
  <div class="modal">
    <div class="modal-header">
      <h3>Registrar beneficiario</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-beneficiario')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group full"><label>Nombre completo *</label><input type="text" placeholder="Nombre completo"></div>
        <div class="form-group"><label>Edad *</label><input type="number" placeholder="0" min="1" max="149"></div>
        <div class="form-group"><label>Teléfono *</label><input type="tel" placeholder="222-XXX-XXXX"></div>
        <div class="form-group"><label>Comunidad *</label><input type="text" placeholder="Colonia, municipio..."></div>
        <div class="form-group full"><label>Dirección *</label><textarea rows="2" placeholder="Dirección completa..."></textarea></div>
        <div class="form-group"><label>Tipo de apoyo *</label><select><option>— Seleccionar —</option><option>Víveres</option><option>Medicamentos</option><option>Ropa</option><option>Útiles escolares</option><option>Múltiple</option></select></div>
        <div class="form-group"><label>Estado</label><select><option>Activo</option><option>En revisión</option><option>Inactivo</option></select></div>
        <div class="form-group"><label>Registrado por *</label><select><option>Eva Sánchez</option><option>Jessica R.</option></select></div>
        <div class="form-group full"><label>Notas adicionales</label><textarea rows="2" placeholder="Observaciones del coordinador social..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-beneficiario')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-beneficiario','beneficiario')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Nueva entrega -->
<div class="modal-overlay" id="modal-entrega">
  <div class="modal">
    <div class="modal-header">
      <h3>Registrar entrega</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-entrega')">✕</button>
    </div>
    <div class="modal-body">
      <div class="alert alert-warning">La cantidad no puede superar el stock disponible en inventario.</div>
      <div class="form-grid">
        <div class="form-group"><label>Donación a entregar *</label><select><option>— Seleccionar —</option><option>#R002 · Monetaria $5,000</option><option>#R003 · Ropa 15 pzas</option><option>#R004 · Medicamentos 3 cajas</option></select></div>
        <div class="form-group"><label>Beneficiario *</label><select><option>— Seleccionar —</option><option>#B001 · Ana M. Pérez</option><option>#B002 · José R. Cruz</option><option>#B003 · María F. Soto</option></select></div>
        <div class="form-group"><label>Cantidad a entregar *</label><input type="number" placeholder="0" min="1"></div>
        <div class="form-group"><label>Fecha de entrega *</label><input type="date"></div>
        <div class="form-group"><label>Usuario responsable *</label><select><option>Jessica R.</option><option>Eva Sánchez</option></select></div>
        <div class="form-group"><label>Estado</label><select><option>Programada</option><option>En camino</option><option>Completada</option></select></div>
        <div class="form-group full"><label>Observaciones</label><textarea rows="2" placeholder="Notas sobre la entrega..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-entrega')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-entrega','entrega')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Nueva campaña -->
<div class="modal-overlay" id="modal-campana">
  <div class="modal">
    <div class="modal-header">
      <h3>Nueva campaña</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-campana')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group full"><label>Nombre *</label><input type="text" placeholder="Ej. Invierno 2026"></div>
        <div class="form-group full"><label>Descripción</label><textarea rows="2" placeholder="Objetivo social de la campaña..."></textarea></div>
        <div class="form-group"><label>Tipo de meta *</label><select><option>Económica</option><option>Material</option><option>Ambas</option></select></div>
        <div class="form-group"><label>Meta económica (MXN)</label><input type="number" placeholder="0.00"></div>
        <div class="form-group"><label>Fecha inicio *</label><input type="date"></div>
        <div class="form-group"><label>Fecha cierre *</label><input type="date"></div>
        <div class="form-group"><label>Estado inicial</label><select><option>Planificada</option><option>Activa</option></select></div>
        <div class="form-group"><label>Creada por *</label><select><option>Eva Sánchez</option><option>Fernando Rosales</option></select></div>
        <div class="form-group full"><label>Meta material (descripción)</label><textarea rows="2" placeholder="Bienes materiales requeridos..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-campana')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-campana','campaña')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Avance de campaña -->
<div class="modal-overlay" id="modal-avance">
  <div class="modal">
    <div class="modal-header">
      <h3>Registrar avance de campaña</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-avance')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group"><label>Campaña *</label><select><option>Invierno 2026</option><option>Víveres Marzo</option><option>Útiles Escolares</option></select></div>
        <div class="form-group"><label>Reportado por *</label><select><option>Eva Sánchez</option><option>Fernando Rosales</option></select></div>
        <div class="form-group"><label>Porcentaje (0-100)</label><input type="number" placeholder="0" min="0" max="100"></div>
        <div class="form-group"><label>Monto recaudado (MXN)</label><input type="number" placeholder="0.00"></div>
        <div class="form-group full"><label>Descripción del avance *</label><textarea rows="3" placeholder="Descripción narrativa del avance logrado..."></textarea></div>
        <div class="form-group full"><label>Evidencia (URL foto o documento)</label><input type="text" placeholder="https://..."></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-avance')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-avance','avance de campaña')">Guardar avance</button>
    </div>
  </div>
</div>

<!-- Modal: Nuevo donador -->
<div class="modal-overlay" id="modal-donador">
  <div class="modal">
    <div class="modal-header">
      <h3>Nuevo donador</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-donador')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group full"><label>Nombre completo *</label><input type="text" placeholder="Nombre o razón social"></div>
        <div class="form-group"><label>Tipo *</label><select><option>Física</option><option>Moral</option></select></div>
        <div class="form-group"><label>Email *</label><input type="email" placeholder="correo@ejemplo.com"></div>
        <div class="form-group"><label>Teléfono</label><input type="tel" placeholder="222-XXX-XXXX"></div>
        <div class="form-group"><label>Estado</label><select><option>Activo</option><option>Inactivo</option></select></div>
        <div class="form-group"><label>Registrado por</label><select><option>Eva Sánchez</option><option>Jessica R.</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-donador')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-donador','donador')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Movimiento de inventario -->
<div class="modal-overlay" id="modal-movimiento">
  <div class="modal">
    <div class="modal-header">
      <h3>Movimiento de inventario</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-movimiento')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group"><label>Tipo de inventario *</label><select><option>Víveres</option><option>Ropa</option><option>Medicamentos</option><option>Útiles escolares</option><option>Monetaria</option></select></div>
        <div class="form-group"><label>Tipo movimiento *</label><select><option>Entrada</option><option>Salida</option><option>Ajuste</option></select></div>
        <div class="form-group"><label>Cantidad *</label><input type="number" placeholder="0" min="1"></div>
        <div class="form-group"><label>Tipo referencia</label><select><option>Donación</option><option>Entrega</option><option>Ajuste manual</option></select></div>
        <div class="form-group full"><label>Motivo</label><textarea rows="2" placeholder="Motivo del movimiento..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-movimiento')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-movimiento','movimiento de inventario')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Nuevo voluntario -->
<div class="modal-overlay" id="modal-voluntario">
  <div class="modal">
    <div class="modal-header">
      <h3>Nuevo voluntario</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-voluntario')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group full"><label>Usuario (nombre) *</label><input type="text" placeholder="Nombre del voluntario"></div>
        <div class="form-group"><label>Teléfono</label><input type="tel" placeholder="222-XXX-XXXX"></div>
        <div class="form-group"><label>Zona asignada</label><input type="text" placeholder="Colonia o sector"></div>
        <div class="form-group"><label>Disponibilidad</label><select><option>Tiempo completo</option><option>Lunes–viernes</option><option>Fines de semana</option><option>Bajo demanda</option></select></div>
        <div class="form-group"><label>Estado</label><select><option>Activo</option><option>Inactivo</option></select></div>
        <div class="form-group"><label>Fecha ingreso</label><input type="date"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-voluntario')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-voluntario','voluntario')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: Asignación de voluntario -->
<div class="modal-overlay" id="modal-asignacion">
  <div class="modal">
    <div class="modal-header">
      <h3>Nueva asignación</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-asignacion')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group"><label>Voluntario *</label><select><option>— Seleccionar —</option><option>#V001 · Jessica R.</option><option>#V002 · Carlos M.</option></select></div>
        <div class="form-group"><label>Entrega *</label><select><option>— Seleccionar —</option><option>#E001 · Víveres</option><option>#E002 · Medicamentos</option><option>#E003 · Ropa</option></select></div>
        <div class="form-group"><label>Asignado por *</label><select><option>Eva Sánchez</option><option>Jessica R.</option></select></div>
        <div class="form-group"><label>Fecha compromiso *</label><input type="date"></div>
        <div class="form-group full"><label>Observaciones</label><textarea rows="2" placeholder="Notas de la asignación..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-asignacion')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-asignacion','asignación')">Asignar</button>
    </div>
  </div>
</div>

<!-- Modal: Reconocimiento -->
<div class="modal-overlay" id="modal-reconocimiento">
  <div class="modal">
    <div class="modal-header">
      <h3>Emitir reconocimiento</h3>
      <button class="btn btn-secondary btn-sm btn-icon" onclick="closeModal('modal-reconocimiento')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group"><label>Donador *</label><select><option>— Seleccionar —</option><option>María García López</option><option>Empresa Alfa S.A.</option><option>Luis Torres Reyes</option></select></div>
        <div class="form-group"><label>Tipo *</label><select><option>Participación</option><option>Destacado</option><option>Nivel</option></select></div>
        <div class="form-group"><label>Campaña asociada</label><select><option>— Sin campaña —</option><option>Invierno 2026</option><option>Víveres Marzo</option></select></div>
        <div class="form-group"><label>Emitido por *</label><select><option>Eva Sánchez</option><option>Fernando Rosales</option></select></div>
        <div class="form-group full"><label>Descripción (aparece en el PDF) *</label><textarea rows="3" placeholder="Texto que aparecerá en el documento PDF..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-reconocimiento')">Cancelar</button>
      <button class="btn btn-primary" onclick="saveAndClose('modal-reconocimiento','reconocimiento')">Emitir reconocimiento</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     TOAST (notificación tipo snackbar)
     ══════════════════════════════════════════ -->
<div id="toast">
  <span>✓</span>
  <span id="toast-msg"></span>
</div>

<!-- JavaScript principal -->
<script src="<?= BASE_URL ?>/public/js/iskalli.js"></script>

</body>
</html>