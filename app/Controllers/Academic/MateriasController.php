<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\Materia;

class MateriasController extends Controller
{
    private Materia $materias;

    public function __construct() { $this->materias = new Materia(); }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/materias/index', ['title' => 'Materias', 'materias' => $this->materias->all((int) $user['id_colegio']), 'success' => flash('success')]);
    }

    public function create(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->form('academic/materias/create', ['materiasPadre' => $this->materias->active((int) $user['id_colegio'])]);
    }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf();
        if (trim($_POST['nombre_materia'] ?? '') === '') { $this->back('/materias/crear', ['El nombre de la materia es obligatorio.']); }
        $this->materias->create((int) $user['id_colegio'], $_POST); flash('success', 'Materia creada correctamente.'); $this->redirect('/materias');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $materia = $this->materias->find($this->id(), (int) $user['id_colegio']);
        if ($materia === null) { $this->notFound(); return; }
        $this->form('academic/materias/edit', ['materia' => $materia, 'materiasPadre' => $this->materias->active((int) $user['id_colegio'])]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $id = $this->id();
        if ($this->materias->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        if (trim($_POST['nombre_materia'] ?? '') === '') { $this->back('/materias/editar?id=' . $id, ['El nombre de la materia es obligatorio.']); }
        $this->materias->update($id, (int) $user['id_colegio'], $_POST); flash('success', 'Materia actualizada correctamente.'); $this->redirect('/materias');
    }

    public function toggle(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $this->materias->toggleStatus($this->id(), (int) $user['id_colegio']);
        flash('success', 'Estado de la materia actualizado.'); $this->redirect('/materias');
    }

    private function form(string $view, array $data = []): void
    {
        $this->view($view, array_merge($data, ['title' => 'Materia', 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }
    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
