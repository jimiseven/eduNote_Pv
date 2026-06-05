<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Dashboard;

class DashboardController extends Controller
{
    private Dashboard $dashboard;

    public function __construct()
    {
        $this->dashboard = new Dashboard();
    }

    public function adminGeneral(): void
    {
        $this->renderForRole('Administrador General', 'dashboard/admin_general', 'Panel General', $this->dashboard->adminGeneral());
    }

    public function adminColegio(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $this->renderForRole('Administrador Colegio', 'dashboard/admin_colegio', 'Panel del Colegio', $this->dashboard->adminColegio((int) $user['id_colegio']));
    }

    public function director(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $this->renderForRole('Director', 'dashboard/director', 'Panel Director', $this->dashboard->director((int) $user['id_colegio']));
    }

    public function secretario(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $this->renderForRole('Secretario', 'dashboard/secretario', 'Panel Secretario', $this->dashboard->secretario((int) $user['id_colegio']));
    }

    public function profesor(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $this->renderForRole('Profesor', 'dashboard/profesor', 'Panel Profesor', $this->dashboard->profesor((int) $user['id_colegio'], (int) $user['id_personal']));
    }

    private function renderForRole(string $role, string $view, string $title, array $metrics = []): void
    {
        Auth::requireLogin();

        if (Auth::mustChangePassword()) {
            $this->redirect('/cuenta/cambiar-contrasena');
        }

        $user = Auth::user();

        if ($user['nombre_rol'] !== $role) {
            $this->redirect($this->dashboardPath($user['nombre_rol']));
        }

        $this->view($view, [
            'title' => $title,
            'user' => $user,
            'metrics' => $metrics,
        ]);
    }

    private function dashboardPath(string $role): string
    {
        return match ($role) {
            'Administrador General' => '/admin-general/dashboard',
            'Administrador Colegio' => '/admin-colegio/dashboard',
            'Director' => '/director/dashboard',
            'Secretario' => '/secretario/dashboard',
            'Profesor' => '/profesor/dashboard',
            default => '/login',
        };
    }
}
