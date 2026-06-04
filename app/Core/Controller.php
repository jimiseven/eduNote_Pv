<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';

        if (!is_file($layoutFile)) {
            echo $content;
            return;
        }

        require $layoutFile;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function forbidden(): void
    {
        http_response_code(403);
        $this->view('errors/403', ['title' => 'Acceso denegado']);
    }

    protected function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['title' => 'Pagina no encontrada']);
    }

    protected function verifyCsrf(): void
    {
        if (!verify_csrf_token()) {
            http_response_code(419);
            $this->view('errors/419', ['title' => 'Sesion invalida'], 'error');
            exit;
        }
    }

    protected function requireRole(string|array $roles): array
    {
        Auth::requireLogin();

        if (!Auth::hasRole($roles)) {
            $this->forbidden();
            exit;
        }

        return Auth::user();
    }
}
