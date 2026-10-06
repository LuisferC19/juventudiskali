<?php
/**
 * controllers/InicioController.php
 * Maneja la ruta por defecto y muestra la landing page.
 */
class InicioController {

    /**
     * Muestra siempre la landing page como pantalla inicial del sistema.
     */
    public function index(): void {
        $titulo_pagina = 'Juventud ISKALI';
        $body_class = 'landing-body';
        require_once 'views/pages/LandingView.php';
    }
}