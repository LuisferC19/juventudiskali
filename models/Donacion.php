<?php
/**
 * models/Donacion.php
 * Modelo de Donación — Solo estructura de datos, sin base de datos.
 */
class Donacion {
    public $id;
    public $donador_id;
    public $tipo;           // Monetaria, Víveres, Ropa, Medicamentos, Útiles escolares
    public $cantidad;       // número o monto
    public $unidad;         // kg, pzas, pesos, cajas…
    public $fecha_recepcion;
    public $campana_id;
    public $registrado_por;
    public $estado;         // Pendiente, Asignada, Entregada
    public $notas;
}