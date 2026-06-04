<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Personal;

class AuthController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        if (Auth::mustChangePassword()) {
            $this->redirect('/cuenta/cambiar-contrasena');
        }

        $this->redirect($this->dashboardPath(Auth::user()['nombre_rol']));
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect($this->dashboardPath(Auth::user()['nombre_rol']));
        }

        $this->view('auth/login', [
            'title' => 'Iniciar sesion',
            'error' => $_SESSION['login_error'] ?? null,
        ], 'auth');

        unset($_SESSION['login_error']);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($usuario === '' || $password === '') {
            $_SESSION['login_error'] = 'Ingresa usuario y contrasena.';
            $this->redirect('/login');
        }

        $personalModel = new Personal();
        $user = $personalModel->findActiveByUsuario($usuario);

        if ($user === null || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Usuario o contrasena incorrectos.';
            $this->redirect('/login');
        }

        Auth::login($user);
        $personalModel->updateLastAccess((int) $user['id_personal']);

        if (Auth::mustChangePassword()) {
            $this->redirect('/cuenta/cambiar-contrasena');
        }

        $this->redirect($this->dashboardPath($user['nombre_rol']));
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
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
