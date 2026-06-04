<?php

namespace App\Controllers\Students;

use App\Core\Controller;
use App\Models\Estudiante;
use App\Models\EstudianteResponsable;
use App\Models\Responsable;

class EstudiantesController extends Controller
{
    private Estudiante $estudiantes;
    private Responsable $responsables;
    private EstudianteResponsable $relaciones;

    public function __construct()
    {
        $this->estudiantes = new Estudiante();
        $this->responsables = new Responsable();
        $this->relaciones = new EstudianteResponsable();
    }

    public function index(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->view('students/estudiantes/index', [
            'title' => 'Estudiantes',
            'estudiantes' => $this->estudiantes->all((int) $user['id_colegio']),
            'success' => flash('success'),
        ]);
    }

    public function create(): void
    {
        $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->form('students/estudiantes/create');
    }

    public function store(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/estudiantes/crear', $errors); }
        $this->estudiantes->create((int) $user['id_colegio'], $_POST);
        flash('success', 'Estudiante creado correctamente.');
        $this->redirect('/estudiantes');
    }

    public function edit(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $estudiante = $this->estudiantes->find($this->id(), (int) $user['id_colegio']);
        if ($estudiante === null) { $this->notFound(); return; }
        $this->form('students/estudiantes/edit', ['estudiante' => $estudiante]);
    }

    public function update(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $id = $this->id();
        if ($this->estudiantes->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio'], $id);
        if ($errors !== []) { $this->back('/estudiantes/editar?id=' . $id, $errors); }
        $this->estudiantes->update($id, (int) $user['id_colegio'], $_POST);
        flash('success', 'Estudiante actualizado correctamente.');
        $this->redirect('/estudiantes');
    }

    public function responsables(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $idEstudiante = $this->id();
        $estudiante = $this->estudiantes->find($idEstudiante, (int) $user['id_colegio']);
        if ($estudiante === null) { $this->notFound(); return; }
        $this->view('students/estudiantes/responsables', [
            'title' => 'Responsables del Estudiante',
            'estudiante' => $estudiante,
            'relaciones' => $this->relaciones->byEstudiante($idEstudiante, (int) $user['id_colegio']),
            'responsables' => $this->responsables->all((int) $user['id_colegio']),
            'errors' => $_SESSION['form_errors'] ?? [],
            'success' => flash('success'),
        ]);
        unset($_SESSION['form_errors']);
    }

    public function storeResponsable(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $idEstudiante = $this->id();
        if ($this->estudiantes->find($idEstudiante, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        if ($this->responsables->find((int) ($_POST['id_responsable'] ?? 0), (int) $user['id_colegio']) === null) {
            $_SESSION['form_errors'] = ['Debes seleccionar un responsable valido.'];
            $this->redirect('/estudiantes/responsables?id=' . $idEstudiante);
        }
        $this->relaciones->create((int) $user['id_colegio'], $idEstudiante, $_POST);
        flash('success', 'Responsable asociado correctamente.');
        $this->redirect('/estudiantes/responsables?id=' . $idEstudiante);
    }

    public function deleteResponsable(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $this->relaciones->delete(max(0, (int) ($_POST['relacion_id'] ?? 0)), (int) $user['id_colegio']);
        flash('success', 'Responsable desvinculado.');
        $this->redirect('/estudiantes/responsables?id=' . $this->id());
    }

    private function validate(array $data, int $idColegio, ?int $ignoreId = null): array
    {
        $errors = [];
        $rude = trim($data['rude'] ?? '');
        if (trim($data['nombres'] ?? '') === '') { $errors[] = 'Los nombres son obligatorios.'; }
        if ($rude === '') { $errors[] = 'El RUDE es obligatorio.'; }
        if ($rude !== '' && $this->estudiantes->rudeExists($idColegio, $rude, $ignoreId)) { $errors[] = 'El RUDE ya esta registrado en este colegio.'; }
        return $errors;
    }

    private function form(string $view, array $data = []): void
    {
        $this->view($view, array_merge($data, ['title' => 'Estudiante', 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
