<?php

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = preg_replace('#/public$#', '', $scriptDir) ?: '';
    $base = $base === '/' ? '' : rtrim($base, '/');

    return $base . $path;
}

function asset(string $path): string
{
    return url('/public/assets/' . ltrim($path, '/'));
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_token(): bool
{
    $token = $_POST['_csrf_token'] ?? '';

    return is_string($token)
        && isset($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $token);
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $message;
}

function is_active(string $path): string
{
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePath = parse_url(url($path), PHP_URL_PATH) ?: $path;

    return str_starts_with(rtrim($current, '/') . '/', rtrim($basePath, '/') . '/') ? 'active' : '';
}

function is_active_group(array $paths): string
{
    foreach ($paths as $path) {
        if (is_active($path) === 'active') {
            return 'active open';
        }
    }

    return '';
}

function is_group_open(array $paths): string
{
    return is_active_group($paths) !== '' ? '' : 'collapsed';
}
