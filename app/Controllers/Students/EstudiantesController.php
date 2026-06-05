<?php

namespace App\Controllers\Students;

use App\Core\Controller;
use App\Models\Estudiante;
use App\Models\EstudianteComplementario;
use App\Models\EstudianteResponsable;
use App\Models\Matricula;
use App\Models\Nota;
use App\Models\Responsable;

class EstudiantesController extends Controller
{
    private Estudiante $estudiantes;
    private EstudianteComplementario $complementarios;
    private Responsable $responsables;
    private EstudianteResponsable $relaciones;
    private Matricula $matriculas;
    private Nota $notas;

    public function __construct()
    {
        $this->estudiantes = new Estudiante();
        $this->complementarios = new EstudianteComplementario();
        $this->responsables = new Responsable();
        $this->relaciones = new EstudianteResponsable();
        $this->matriculas = new Matricula();
        $this->notas = new Nota();
    }

    public function index(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'estado' => trim($_GET['estado'] ?? ''),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->estudiantes->paginate((int) $user['id_colegio'], $filters, $page, 10);

        $this->view('students/estudiantes/index', [
            'title' => 'Estudiantes',
            'estudiantes' => $pagination['data'],
            'filters' => $filters,
            'pagination' => $pagination,
            'success' => flash('success'),
        ]);
    }

    public function create(): void
    {
        $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->form('students/estudiantes/create');
    }

    public function show(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $idEstudiante = $this->id();
        $idColegio = (int) $user['id_colegio'];
        $estudiante = $this->estudiantes->find($idEstudiante, $idColegio);

        if ($estudiante === null) {
            $this->notFound();
            return;
        }

        $notas = $this->notas->byEstudiante($idEstudiante, $idColegio);
        $promedio = $notas === [] ? null : array_sum(array_map(static fn (array $nota): float => (float) $nota['nota'], $notas)) / count($notas);

        $this->view('students/estudiantes/show', [
            'title' => 'Ficha del Estudiante',
            'estudiante' => $estudiante,
            'responsables' => $this->relaciones->byEstudiante($idEstudiante, $idColegio),
            'matriculas' => $this->matriculas->byEstudiante($idEstudiante, $idColegio),
            'notas' => $notas,
            'promedio' => $promedio,
            'complementarios' => $this->complementarios->all($idEstudiante),
        ]);
    }

    public function complementarios(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $idEstudiante = $this->id();
        $idColegio = (int) $user['id_colegio'];
        $estudiante = $this->estudiantes->find($idEstudiante, $idColegio);

        if ($estudiante === null) {
            $this->notFound();
            return;
        }

        $this->view('students/estudiantes/complementarios', [
            'title' => 'Datos Complementarios',
            'estudiante' => $estudiante,
            'data' => $_SESSION['old'] ?? $this->complementarios->all($idEstudiante),
            'success' => flash('success'),
        ]);
        unset($_SESSION['old']);
    }

    public function updateComplementarios(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $idEstudiante = $this->id();
        $idColegio = (int) $user['id_colegio'];

        if ($this->estudiantes->find($idEstudiante, $idColegio) === null) {
            $this->notFound();
            return;
        }

        $this->complementarios->save($idEstudiante, $_POST);
        flash('success', 'Datos complementarios actualizados correctamente.');
        $this->redirect('/estudiantes/complementarios?id=' . $idEstudiante);
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
