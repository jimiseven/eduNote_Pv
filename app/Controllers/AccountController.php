<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Personal;

class AccountController extends Controller
{
    public function changePassword(): void
    {
        Auth::requireLogin();

        $this->view('account/change_password', [
            'title' => 'Cambiar Contrasena',
            'user' => Auth::user(),
            'errors' => $_SESSION['form_errors'] ?? [],
            'success' => flash('success'),
        ]);

        unset($_SESSION['form_errors']);
    }

    public function updatePassword(): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();

        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $errors = [];

        if (strlen($password) < 6) {
            $errors[] = 'La nueva contrasena debe tener al menos 6 caracteres.';
        }

        if ($password !== $passwordConfirmation) {
            $errors[] = 'La confirmacion de contrasena no coincide.';
        }

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/cuenta/cambiar-contrasena');
        }

        $user = Auth::user();
        $personal = new Personal();
        $personal->updatePassword((int) $user['id_personal'], $password, false);
        Auth::refreshUser(['debe_cambiar_password' => 0]);

        flash('success', 'Contrasena actualizada correctamente.');
        $this->redirect('/');
    }
}
