<?php
/**
 * controllers/CampanasController.php
 * Gestiona la vista de campañas.
 *
 * CORRECCIÓN: verificarSesion() ahora usa $_SESSION['id_usuario'] en lugar de
 * $_SESSION['usuario'], igual que el resto de controladores del sistema.
 * Usar 'usuario' era incorrecto: esa clave contiene el nombre completo (string),
 * mientras que 'id_usuario' es la que AuthController guarda al hacer login.
 */
class CampanasController
{
    private function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    public function index(): void
    {
        $this->verificarSesion();

        $pagina_activa = 'campanas';
        $titulo_pagina = 'Campañas';
        require_once 'views/pages/CampanasView.php';
    }
}