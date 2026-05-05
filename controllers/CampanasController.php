<?php
/**
 * controllers/CampanasController.php
 * Gestiona la vista de campañas.
 */
class CampanasController {

    private function verificarSesion(): void {
        if (empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    public function index(): void {
        $this->verificarSesion();
        $pagina_activa = 'campanas';
        $titulo_pagina = 'Campañas';
        require_once 'views/pages/campanas.php';
    }
}