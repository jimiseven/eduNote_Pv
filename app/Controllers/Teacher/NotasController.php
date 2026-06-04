<?php

namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Models\Evaluacion;
use App\Models\Nota;

class NotasController extends Controller
{
    private Evaluacion $evaluaciones;
    private Nota $notas;

    public function __construct()
    {
        $this->evaluaciones = new Evaluacion();
        $this->notas = new Nota();
    }

    public function edit(): void
    {
        $user = $this->requireRole('Profesor');
        $evaluacion = $this->evaluaciones->findForTeacher($this->id(), (int) $user['id_colegio'], (int) $user['id_personal']);
        if ($evaluacion === null) { $this->notFound(); return; }
        $this->view('teacher/notas/edit', [
            'title' => 'Cargar Notas',
            'evaluacion' => $evaluacion,
            'estudiantes' => $this->notas->studentsForEvaluation((int) $user['id_colegio'], $evaluacion),
            'errors' => $_SESSION['form_errors'] ?? [],
            'success' => flash('success'),
        ]);
        unset($_SESSION['form_errors']);
    }

    public function update(): void
    {
        $user = $this->requireRole('Profesor');
        $this->verifyCsrf();
        $idEvaluacion = $this->id();
        $evaluacion = $this->evaluaciones->findForTeacher($idEvaluacion, (int) $user['id_colegio'], (int) $user['id_personal']);
        if ($evaluacion === null) { $this->notFound(); return; }
        if ($evaluacion['estado'] === 'cerrada') { $_SESSION['form_errors'] = ['La evaluacion esta cerrada.']; $this->redirect('/profesor/notas?id=' . $idEvaluacion); }

        $notas = $_POST['notas'] ?? [];
        foreach ($notas as $nota) {
            if ($nota !== '' && ((float) $nota < 0 || (float) $nota > 100)) {
                $_SESSION['form_errors'] = ['Todas las notas deben estar entre 0 y 100.'];
                $this->redirect('/profesor/notas?id=' . $idEvaluacion);
            }
        }

        $this->notas->saveBulk((int) $user['id_colegio'], $idEvaluacion, $notas, $_POST['comentarios'] ?? []);
        flash('success', 'Notas guardadas correctamente.');
        $this->redirect('/profesor/notas?id=' . $idEvaluacion);
    }

    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
