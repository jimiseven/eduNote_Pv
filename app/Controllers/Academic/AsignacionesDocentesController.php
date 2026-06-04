<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\AsignacionDocente;
use App\Models\CursoMateria;
use App\Models\Gestion;
use App\Models\Personal;

class AsignacionesDocentesController extends Controller
{
    private AsignacionDocente $asignaciones;
    private Gestion $gestiones;
    private Personal $personal;
    private CursoMateria $cursoMaterias;

    public function __construct() { $this->asignaciones = new AsignacionDocente(); $this->gestiones = new Gestion(); $this->personal = new Personal(); $this->cursoMaterias = new CursoMateria(); }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/asignaciones_docentes/index', ['title' => 'Asignaciones Docentes', 'asignaciones' => $this->asignaciones->all((int) $user['id_colegio']), 'success' => flash('success')]);
    }

    public function create(): void { $this->showForm('academic/asignaciones_docentes/create'); }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/asignaciones-docentes/crear', $errors); }
        $this->asignaciones->create((int) $user['id_colegio'], $_POST); flash('success', 'Docente asignado correctamente.'); $this->redirect('/asignaciones-docentes');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $asignacion = $this->asignaciones->find($this->id(), (int) $user['id_colegio']);
        if ($asignacion === null) { $this->notFound(); return; }
        $this->showForm('academic/asignaciones_docentes/edit', ['asignacion' => $asignacion]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf(); $id = $this->id();
        if ($this->asignaciones->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio']); if ($errors !== []) { $this->back('/asignaciones-docentes/editar?id=' . $id, $errors); }
        $this->asignaciones->update($id, (int) $user['id_colegio'], $_POST); flash('success', 'Asignacion docente actualizada.'); $this->redirect('/asignaciones-docentes');
    }

    private function showForm(string $view, array $data = []): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view($view, array_merge($data, ['title' => 'Asignacion Docente', 'gestiones' => $this->gestiones->all((int) $user['id_colegio']), 'docentes' => $this->personal->teachers((int) $user['id_colegio']), 'cursoMaterias' => $this->cursoMaterias->active((int) $user['id_colegio']), 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }
    private function validate(array $data, int $idColegio): array
    {
        $errors = [];
        if ($this->gestiones->find((int) ($data['id_gestion'] ?? 0), $idColegio) === null) { $errors[] = 'La gestion seleccionada no es valida.'; }
        if ($this->cursoMaterias->find((int) ($data['id_curso_materia'] ?? 0), $idColegio) === null) { $errors[] = 'La materia del curso seleccionada no es valida.'; }
        $docenteIds = array_map('intval', array_column($this->personal->teachers($idColegio), 'id_personal'));
        if (!in_array((int) ($data['id_personal'] ?? 0), $docenteIds, true)) { $errors[] = 'Debes seleccionar un docente valido del colegio.'; }
        return $errors;
    }
    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
