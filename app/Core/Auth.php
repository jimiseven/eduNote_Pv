<?php

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id_personal' => (int) $user['id_personal'],
            'id_colegio' => $user['id_colegio'] !== null ? (int) $user['id_colegio'] : null,
            'id_rol' => (int) $user['id_rol'],
            'nombre_rol' => $user['nombre_rol'],
            'usuario' => $user['usuario'],
            'nombres' => $user['nombres'],
            'apellidos' => $user['apellidos'],
            'colegio' => $user['colegio'] ?? null,
            'debe_cambiar_password' => (int) ($user['debe_cambiar_password'] ?? 0),
        ];
    }

    public static function refreshUser(array $user): void
    {
        if (!self::check()) {
            return;
        }

        $_SESSION['user'] = array_merge($_SESSION['user'], $user);
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . url('/login'));
            exit;
        }

        if (self::mustChangePassword() && !self::isPasswordChangeRoute()) {
            header('Location: ' . url('/cuenta/cambiar-contrasena'));
            exit;
        }
    }

    public static function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        $user = self::user();

        return $user !== null && in_array($user['nombre_rol'] ?? '', $roles, true);
    }

    public static function mustChangePassword(): bool
    {
        return self::check() && (int) ($_SESSION['user']['debe_cambiar_password'] ?? 0) === 1;
    }

    private static function isPasswordChangeRoute(): bool
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return str_contains($path, '/cuenta/cambiar-contrasena')
            || str_contains($path, '/logout');
    }
}
