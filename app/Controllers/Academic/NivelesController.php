<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\Nivel;

class NivelesController extends Controller
{
    private Nivel $niveles;

    public function __construct() { $this->niveles = new Nivel(); }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/niveles/index', ['title' => 'Niveles', 'niveles' => $this->niveles->all((int) $user['id_colegio']), 'success' => flash('success')]);
    }

    public function create(): void { $this->requireRole('Administrador Colegio'); $this->form('academic/niveles/create'); }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf();
        if (trim($_POST['nombre_nivel'] ?? '') === '') { $this->back('/niveles/crear', ['El nombre del nivel es obligatorio.']); }
        $this->niveles->create((int) $user['id_colegio'], $_POST); flash('success', 'Nivel creado correctamente.'); $this->redirect('/niveles');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $nivel = $this->niveles->find($this->id(), (int) $user['id_colegio']);
        if ($nivel === null) { $this->notFound(); return; }
        $this->form('academic/niveles/edit', ['nivel' => $nivel]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $id = $this->id();
        if ($this->niveles->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        if (trim($_POST['nombre_nivel'] ?? '') === '') { $this->back('/niveles/editar?id=' . $id, ['El nombre del nivel es obligatorio.']); }
        $this->niveles->update($id, (int) $user['id_colegio'], $_POST); flash('success', 'Nivel actualizado correctamente.'); $this->redirect('/niveles');
    }

    private function form(string $view, array $data = []): void
    {
        $this->view($view, array_merge($data, ['title' => 'Nivel', 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
