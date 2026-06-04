<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\Curso;
use App\Models\CursoMateria;
use App\Models\Materia;

class CursosMateriasController extends Controller
{
    private CursoMateria $cursoMaterias;
    private Curso $cursos;
    private Materia $materias;

    public function __construct() { $this->cursoMaterias = new CursoMateria(); $this->cursos = new Curso(); $this->materias = new Materia(); }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/cursos_materias/index', ['title' => 'Materias por Curso', 'items' => $this->cursoMaterias->all((int) $user['id_colegio']), 'success' => flash('success')]);
    }

    public function create(): void { $this->showForm('academic/cursos_materias/create'); }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/cursos-materias/crear', $errors); }
        $this->cursoMaterias->create((int) $user['id_colegio'], $_POST); flash('success', 'Materia asignada al curso.'); $this->redirect('/cursos-materias');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $item = $this->cursoMaterias->find($this->id(), (int) $user['id_colegio']);
        if ($item === null) { $this->notFound(); return; }
        $this->showForm('academic/cursos_materias/edit', ['item' => $item]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $id = $this->id();
        if ($this->cursoMaterias->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio']); if ($errors !== []) { $this->back('/cursos-materias/editar?id=' . $id, $errors); }
        $this->cursoMaterias->update($id, (int) $user['id_colegio'], $_POST); flash('success', 'Asignacion actualizada.'); $this->redirect('/cursos-materias');
    }

    private function showForm(string $view, array $data = []): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view($view, array_merge($data, ['title' => 'Materia por Curso', 'cursos' => $this->cursos->all((int) $user['id_colegio']), 'materias' => $this->materias->active((int) $user['id_colegio']), 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }
    private function validate(array $data, int $idColegio): array
    {
        $errors = [];
        if ($this->cursos->find((int) ($data['id_curso'] ?? 0), $idColegio) === null) { $errors[] = 'El curso seleccionado no es valido.'; }
        if ($this->materias->find((int) ($data['id_materia'] ?? 0), $idColegio) === null) { $errors[] = 'La materia seleccionada no es valida.'; }
        return $errors;
    }
    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
