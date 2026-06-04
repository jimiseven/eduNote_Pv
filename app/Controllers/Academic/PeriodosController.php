<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\Gestion;
use App\Models\PeriodoAcademico;

class PeriodosController extends Controller
{
    private PeriodoAcademico $periodos;
    private Gestion $gestiones;

    public function __construct()
    {
        $this->periodos = new PeriodoAcademico();
        $this->gestiones = new Gestion();
    }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/periodos/index', [
            'title' => 'Periodos Academicos',
            'periodos' => $this->periodos->all((int) $user['id_colegio']),
            'success' => flash('success'),
        ]);
    }

    public function create(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->form('academic/periodos/create', ['gestiones' => $this->gestiones->all((int) $user['id_colegio'])]);
    }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->verifyCsrf();
        $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/periodos/crear', $errors); }
        $this->periodos->create((int) $user['id_colegio'], $_POST);
        flash('success', 'Periodo creado correctamente.');
        $this->redirect('/periodos');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $periodo = $this->periodos->find($this->id(), (int) $user['id_colegio']);
        if ($periodo === null) { $this->notFound(); return; }
        $this->form('academic/periodos/edit', ['periodo' => $periodo, 'gestiones' => $this->gestiones->all((int) $user['id_colegio'])]);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->verifyCsrf();
        $id = $this->id();
        if ($this->periodos->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) { $this->back('/periodos/editar?id=' . $id, $errors); }
        $this->periodos->update($id, (int) $user['id_colegio'], $_POST);
        flash('success', 'Periodo actualizado correctamente.');
        $this->redirect('/periodos');
    }

    private function form(string $view, array $data = []): void
    {
        $this->view($view, array_merge($data, ['title' => 'Periodo Academico', 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    private function validate(array $data, int $idColegio): array
    {
        $errors = [];
        if ($this->gestiones->find((int) ($data['id_gestion'] ?? 0), $idColegio) === null) { $errors[] = 'La gestion seleccionada no es valida.'; }
        if ((int) ($data['numero_periodo'] ?? 0) <= 0) { $errors[] = 'El numero de periodo debe ser mayor a cero.'; }
        if (trim($data['nombre'] ?? '') === '') { $errors[] = 'El nombre es obligatorio.'; }
        return $errors;
    }

    private function back(string $path, array $errors): void
    {
        $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path);
    }

    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
