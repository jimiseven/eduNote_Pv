<?php

namespace App\Controllers\Reports;

use App\Core\Controller;
use App\Models\Gestion;
use App\Models\Reporte;

class ReportesController extends Controller
{
    private Reporte $reportes;
    private Gestion $gestiones;

    public function __construct()
    {
        $this->reportes = new Reporte();
        $this->gestiones = new Gestion();
    }

    public function index(): void
    {
        $this->requireReportRole();
        $this->view('reports/index', ['title' => 'Reportes']);
    }

    public function estudiantesPorCurso(): void
    {
        $user = $this->requireReportRole();
        $idGestion = $this->selectedGestion();
        $this->view('reports/estudiantes_por_curso', [
            'title' => 'Estudiantes por Curso',
            'rows' => $this->reportes->estudiantesPorCurso((int) $user['id_colegio'], $idGestion),
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'selectedGestion' => $idGestion,
        ]);
    }

    public function docentesPorMateria(): void
    {
        $user = $this->requireReportRole();
        $idGestion = $this->selectedGestion();
        $this->view('reports/docentes_por_materia', [
            'title' => 'Docentes por Materia',
            'rows' => $this->reportes->docentesPorMateria((int) $user['id_colegio'], $idGestion),
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'selectedGestion' => $idGestion,
        ]);
    }

    public function responsablesPorEstudiante(): void
    {
        $user = $this->requireReportRole();
        $this->view('reports/responsables_por_estudiante', [
            'title' => 'Responsables por Estudiante',
            'rows' => $this->reportes->responsablesPorEstudiante((int) $user['id_colegio']),
        ]);
    }

    public function notasPorEvaluacion(): void
    {
        $user = $this->requireReportRole();
        $idGestion = $this->selectedGestion();
        $this->view('reports/notas_por_evaluacion', [
            'title' => 'Notas por Evaluacion',
            'rows' => $this->reportes->notasPorEvaluacion((int) $user['id_colegio'], $idGestion),
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'selectedGestion' => $idGestion,
        ]);
    }

    private function requireReportRole(): array
    {
        return $this->requireRole(['Administrador Colegio', 'Director', 'Secretario']);
    }

    private function selectedGestion(): ?int
    {
        $value = (int) ($_GET['gestion'] ?? 0);
        return $value > 0 ? $value : null;
    }
}
