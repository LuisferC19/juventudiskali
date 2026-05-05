<?php
/**
 * controllers/InicioController.php
 * Maneja la ruta por defecto — redirige al login.
 */
class InicioController {

    /**
     * Muestra la landing page para usuarios no autenticados.
     * Si ya hay sesión activa, redirige al dashboard.
     */
    public function index(): void {
        // Si el usuario ya tiene sesión activa, redirigir directamente al dashboard
        if (!empty($_SESSION['id_usuario'])) {
            session_write_close();
            header('Location: ' . BASE_URL . '/index.php?pagina=dashboard');
            exit;
        }

        $titulo_pagina = 'Juventud ISKALI';
        $body_class = 'landing-body';
        require_once 'views/pages/landing.php';
    }
}