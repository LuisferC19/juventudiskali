<?php
/**
 * models/Campana.php
 * Modelo de Campaña — Solo estructura de datos, sin base de datos.
 */
class Campana {
    public $id;
    public $nombre;
    public $descripcion;
    public $tipo_meta;      // Económica, Material, Ambas
    public $meta_economica; // en pesos MXN
    public $meta_material;  // descripción de bienes
    public $fecha_inicio;
    public $fecha_cierre;
    public $estado;         // Planificada, Activa, Cerrada
    public $creada_por;
    public $imagen_url;
    public $avance_pct;     // porcentaje 0-100
}