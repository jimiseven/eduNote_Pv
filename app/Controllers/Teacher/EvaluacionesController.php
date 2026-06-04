<?php

namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Models\AsignacionDocente;
use App\Models\Evaluacion;
use App\Models\PeriodoAcademico;

class EvaluacionesController extends Controller
{
    private AsignacionDocente $asignaciones;
    private Evaluacion $evaluaciones;
    private PeriodoAcademico $periodos;

    public function __construct()
    {
        $this->asignaciones = new AsignacionDocente();
        $this->evaluaciones = new Evaluacion();
        $this->periodos = new PeriodoAcademico();
    }

    public function create(): void
    {
        $this->showForm('teacher/evaluaciones/create');
    }

    public function store(): void
    {
        $user = $this->requireRole('Profesor');
        $this->verifyCsrf();
        $errors = $this->validate($_POST, (int) $user['id_colegio'], (int) $user['id_personal']);
        if ($errors !== []) { $this->back('/profesor/evaluaciones/crear', $errors); }
        $this->evaluaciones->create((int) $user['id_colegio'], $_POST);
        flash('success', 'Evaluacion creada correctamente.');
        $this->redirect('/profesor/evaluaciones');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Profesor');
        $evaluacion = $this->evaluaciones->findForTeacher($this->id(), (int) $user['id_colegio'], (int) $user['id_personal']);
        if ($evaluacion === null) { $this->notFound(); return; }
        $this->showForm('teacher/evaluaciones/edit', ['evaluacion' => $evaluacion]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Profesor');
        $this->verifyCsrf();
        $id = $this->id();
        if ($this->evaluaciones->findForTeacher($id, (int) $user['id_colegio'], (int) $user['id_personal']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio'], (int) $user['id_personal']);
        if ($errors !== []) { $this->back('/profesor/evaluaciones/editar?id=' . $id, $errors); }
        $this->evaluaciones->update($id, (int) $user['id_colegio'], $_POST);
        flash('success', 'Evaluacion actualizada correctamente.');
        $this->redirect('/profesor/evaluaciones');
    }

    private function showForm(string $view, array $data = []): void
    {
        $user = $this->requireRole('Profesor');
        $this->view($view, array_merge($data, [
            'title' => 'Evaluacion',
            'asignaciones' => $this->asignaciones->byTeacher((int) $user['id_colegio'], (int) $user['id_personal']),
            'periodos' => $this->periodos->all((int) $user['id_colegio']),
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    private function validate(array $data, int $idColegio, int $idPersonal): array
    {
        $errors = [];
        if ($this->asignaciones->findForTeacher((int) ($data['id_asignacion'] ?? 0), $idColegio, $idPersonal) === null) { $errors[] = 'La asignacion seleccionada no es valida.'; }
        if (trim($data['nombre'] ?? '') === '') { $errors[] = 'El nombre de la evaluacion es obligatorio.'; }
        $ponderacion = (float) ($data['ponderacion'] ?? 0);
        if ($ponderacion <= 0 || $ponderacion > 100) { $errors[] = 'La ponderacion debe estar entre 1 y 100.'; }
        return $errors;
    }

    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
