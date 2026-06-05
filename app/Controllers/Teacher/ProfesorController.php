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
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'tipo' => trim($_GET['tipo'] ?? ''),
            'estado' => trim($_GET['estado'] ?? ''),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->evaluaciones->paginateByTeacher((int) $user['id_colegio'], (int) $user['id_personal'], $filters, $page, 10);

        $this->view('teacher/evaluaciones/index', [
            'title' => 'Mis Evaluaciones',
            'evaluaciones' => $pagination['data'],
            'filters' => $filters,
            'pagination' => $pagination,
            'success' => flash('success'),
        ]);
    }
}
