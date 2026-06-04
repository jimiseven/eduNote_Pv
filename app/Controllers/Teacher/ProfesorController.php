<?php

namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Models\AsignacionDocente;
use App\Models\Evaluacion;

class ProfesorController extends Controller
{
    private AsignacionDocente $asignaciones;
    private Evaluacion $evaluaciones;

    public function __construct()
    {
        $this->asignaciones = new AsignacionDocente();
        $this->evaluaciones = new Evaluacion();
    }

    public function materias(): void
    {
        $user = $this->requireRole('Profesor');
        $this->view('teacher/materias/index', [
            'title' => 'Mis Materias',
            'asignaciones' => $this->asignaciones->byTeacher((int) $user['id_colegio'], (int) $user['id_personal']),
            'success' => flash('success'),
        ]);
    }

    public function evaluaciones(): void
    {
        $user = $this->requireRole('Profesor');
        $this->view('teacher/evaluaciones/index', [
            'title' => 'Mis Evaluaciones',
            'evaluaciones' => $this->evaluaciones->byTeacher((int) $user['id_colegio'], (int) $user['id_personal']),
            'success' => flash('success'),
        ]);
    }
}
