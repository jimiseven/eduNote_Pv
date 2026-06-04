<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\Curso;
use App\Models\Nivel;

class CursosController extends Controller
{
    private Curso $cursos;
    private Nivel $niveles;

    public function __construct() { $this->cursos = new Curso(); $this->niveles = new Nivel(); }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/cursos/index', ['title' => 'Cursos', 'cursos' => $this->cursos->all((int) $user['id_colegio']), 'success' => flash('success')]);
    }

    public function create(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->form('academic/cursos/create', ['niveles' => $this->niveles->all((int) $user['id_colegio'])]);
    }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/cursos/crear', $errors); }
        $this->cursos->create((int) $user['id_colegio'], $_POST); flash('success', 'Curso creado correctamente.'); $this->redirect('/cursos');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $curso = $this->cursos->find($this->id(), (int) $user['id_colegio']);
        if ($curso === null) { $this->notFound(); return; }
        $this->form('academic/cursos/edit', ['curso' => $curso, 'niveles' => $this->niveles->all((int) $user['id_colegio'])]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $id = $this->id();
        if ($this->cursos->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio']); if ($errors !== []) { $this->back('/cursos/editar?id=' . $id, $errors); }
        $this->cursos->update($id, (int) $user['id_colegio'], $_POST); flash('success', 'Curso actualizado correctamente.'); $this->redirect('/cursos');
    }

    public function toggle(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $this->cursos->toggleStatus($this->id(), (int) $user['id_colegio']);
        flash('success', 'Estado del curso actualizado.'); $this->redirect('/cursos');
    }

    private function validate(array $data, int $idColegio): array
    {
        $errors = [];
        if ($this->niveles->find((int) ($data['id_nivel'] ?? 0), $idColegio) === null) { $errors[] = 'El nivel seleccionado no es valido.'; }
        if ((int) ($data['grado'] ?? 0) <= 0) { $errors[] = 'El grado debe ser mayor a cero.'; }
        if (trim($data['paralelo'] ?? '') === '') { $errors[] = 'El paralelo es obligatorio.'; }
        return $errors;
    }

    private function form(string $view, array $data = []): void
    {
        $this->view($view, array_merge($data, ['title' => 'Curso', 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }
    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
