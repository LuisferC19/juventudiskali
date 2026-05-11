<?php
/**
 * controllers/DashboardController.php
 * Maneja el dashboard principal y sub-páginas del sistema.
 * No hay lógica de BD; solo carga vistas con datos estáticos.
 */
class DashboardController {

    /**
     * Verifica que haya sesión activa; si no, redirige al login.
     */
    private function verificarSesion(): void {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . '/index.php?pagina=login');
            exit;
        }
    }

    // ——— Dashboard principal ———
    public function index(): void {
        $this->verificarSesion();
        $pagina_activa = 'dashboard';
        $titulo_pagina = 'Dashboard';
        require_once 'views/pages/DashboardView.php';
    }

    // ——— Donadores ———
    public function donadores(): void {
        $this->verificarSesion();
        $pagina_activa = 'donadores';
        $titulo_pagina = 'Donadores';
        require_once 'views/pages/DonadoresView.php';
    }

    // ——— Donaciones ———
    public function donaciones(): void {
        $this->verificarSesion();
        $pagina_activa = 'donaciones';
        $titulo_pagina = 'Donaciones';
        require_once 'views/pages/DonacionesView.php';
    }

    // ——— Beneficiarios ———
    public function beneficiarios(): void {
        $this->verificarSesion();
        $pagina_activa = 'beneficiarios';
        $titulo_pagina = 'Beneficiarios';
        require_once 'views/pages/BeneficiariosView.php';
    }

    // ——— Entregas ———
    public function entregas(): void {
        $this->verificarSesion();
        $pagina_activa = 'entregas';
        $titulo_pagina = 'Entregas';
        require_once 'views/pages/EntregasView.php';
    }

    // ——— Inventario ———
    public function inventario(): void {
        $this->verificarSesion();
        $pagina_activa = 'inventario';
        $titulo_pagina = 'Inventario';
        require_once 'views/pages/InventarioView.php';
    }

    // ——— Voluntarios ———
    public function voluntarios(): void {
        $this->verificarSesion();
        $pagina_activa = 'voluntarios';
        $titulo_pagina = 'Voluntarios';
        require_once 'views/pages/VoluntariosView.php';
    }

    // ——— Gamificación ———
    public function gamificacion(): void {
        $this->verificarSesion();
        $pagina_activa = 'gamificacion';
        $titulo_pagina = 'Gamificación';
        require_once 'views/pages/GamificacionView.php';
    }
}