<?php

namespace App\Controllers\Students;

use App\Core\Controller;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Gestion;
use App\Models\Matricula;

class MatriculasController extends Controller
{
    private Matricula $matriculas;
    private Estudiante $estudiantes;
    private Curso $cursos;
    private Gestion $gestiones;

    public function __construct()
    {
        $this->matriculas = new Matricula();
        $this->estudiantes = new Estudiante();
        $this->cursos = new Curso();
        $this->gestiones = new Gestion();
    }

    public function index(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'id_gestion' => (int) ($_GET['id_gestion'] ?? 0),
            'estado' => trim($_GET['estado'] ?? ''),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->matriculas->paginate((int) $user['id_colegio'], $filters, $page, 10);

        $this->view('students/matriculas/index', [
            'title' => 'Matriculas',
            'matriculas' => $pagination['data'],
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'filters' => $filters,
            'pagination' => $pagination,
            'success' => flash('success'),
        ]);
    }

    public function create(): void
    {
        $this->showForm('students/matriculas/create');
    }

    public function store(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/matriculas/crear', $errors); }
        $this->matriculas->create((int) $user['id_colegio'], $_POST);
        flash('success', 'Matricula registrada correctamente.');
        $this->redirect('/matriculas');
    }

    public function edit(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $matricula = $this->matriculas->find($this->id(), (int) $user['id_colegio']);
        if ($matricula === null) { $this->notFound(); return; }
        $this->showForm('students/matriculas/edit', ['matricula' => $matricula]);
    }

    public function update(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $id = $this->id();
        if ($this->matriculas->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio'], $id);
        if ($errors !== []) { $this->back('/matriculas/editar?id=' . $id, $errors); }
        $this->matriculas->update($id, (int) $user['id_colegio'], $_POST);
        flash('success', 'Matricula actualizada correctamente.');
        $this->redirect('/matriculas');
    }

    private function showForm(string $view, array $data = []): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->view($view, array_merge($data, [
            'title' => 'Matricula',
            'estudiantes' => $this->estudiantes->all((int) $user['id_colegio']),
            'cursos' => $this->cursos->all((int) $user['id_colegio']),
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    private function validate(array $data, int $idColegio, ?int $ignoreId = null): array
    {
        $errors = [];
        $idEstudiante = (int) ($data['id_estudiante'] ?? 0);
        $idGestion = (int) ($data['id_gestion'] ?? 0);
        if ($this->estudiantes->find($idEstudiante, $idColegio) === null) { $errors[] = 'El estudiante seleccionado no es valido.'; }
        if ($this->cursos->find((int) ($data['id_curso'] ?? 0), $idColegio) === null) { $errors[] = 'El curso seleccionado no es valido.'; }
        if ($this->gestiones->find($idGestion, $idColegio) === null) { $errors[] = 'La gestion seleccionada no es valida.'; }
        if ($idEstudiante > 0 && $idGestion > 0 && $this->matriculas->studentHasGestion($idEstudiante, $idGestion, $ignoreId)) {
            $errors[] = 'El estudiante ya tiene una matricula registrada en esta gestion.';
        }
        return $errors;
    }

    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
