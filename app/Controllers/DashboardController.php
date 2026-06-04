<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class DashboardController extends Controller
{
    public function adminGeneral(): void
    {
        $this->renderForRole('Administrador General', 'dashboard/admin_general', 'Panel General');
    }

    public function adminColegio(): void
    {
        $this->renderForRole('Administrador Colegio', 'dashboard/admin_colegio', 'Panel del Colegio');
    }

    public function director(): void
    {
        $this->renderForRole('Director', 'dashboard/director', 'Panel Director');
    }

    public function secretario(): void
    {
        $this->renderForRole('Secretario', 'dashboard/secretario', 'Panel Secretario');
    }

    public function profesor(): void
    {
        $this->renderForRole('Profesor', 'dashboard/profesor', 'Panel Profesor');
    }

    private function renderForRole(string $role, string $view, string $title): void
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
