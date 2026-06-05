<?php

namespace App\Controllers\Students;

use App\Core\Controller;
use App\Models\Responsable;

class ResponsablesController extends Controller
{
    private Responsable $responsables;

    public function __construct() { $this->responsables = new Responsable(); }

    public function index(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $filters = ['q' => trim($_GET['q'] ?? '')];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->responsables->paginate((int) $user['id_colegio'], $filters, $page, 10);

        $this->view('students/responsables/index', [
            'title' => 'Responsables',
            'responsables' => $pagination['data'],
            'filters' => $filters,
            'pagination' => $pagination,
            'success' => flash('success'),
        ]);
    }

    public function create(): void
    {
        $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->form('students/responsables/create');
    }

    public function store(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        if (trim($_POST['nombres'] ?? '') === '') { $this->back('/responsables/crear', ['Los nombres son obligatorios.']); }
        $this->responsables->create((int) $user['id_colegio'], $_POST);
        flash('success', 'Responsable creado correctamente.');
        $this->redirect('/responsables');
    }

    public function edit(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $responsable = $this->responsables->find($this->id(), (int) $user['id_colegio']);
        if ($responsable === null) { $this->notFound(); return; }
        $this->form('students/responsables/edit', ['responsable' => $responsable]);
    }

    public function update(): void
    {
        $user = $this->requireRole(['Administrador Colegio', 'Secretario']);
        $this->verifyCsrf();
        $id = $this->id();
        if ($this->responsables->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        if (trim($_POST['nombres'] ?? '') === '') { $this->back('/responsables/editar?id=' . $id, ['Los nombres son obligatorios.']); }
        $this->responsables->update($id, (int) $user['id_colegio'], $_POST);
        flash('success', 'Responsable actualizado correctamente.');
        $this->redirect('/responsables');
    }

    private function form(string $view, array $data = []): void
    {
        $this->view($view, array_merge($data, ['title' => 'Responsable', 'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['old'] ?? []]));
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }
    private function back(string $path, array $errors): void { $_SESSION['form_errors'] = $errors; $_SESSION['old'] = $_POST; $this->redirect($path); }
    private function id(): int { return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0)); }
}
