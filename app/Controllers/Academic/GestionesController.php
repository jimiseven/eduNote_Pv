<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\Gestion;

class GestionesController extends Controller
{
    private Gestion $gestiones;

    public function __construct()
    {
        $this->gestiones = new Gestion();
    }

    public function index(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/gestiones/index', [
            'title' => 'Gestiones',
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'success' => flash('success'),
        ]);
    }

    public function create(): void
    {
        $this->requireRole('Administrador Colegio');
        $this->view('academic/gestiones/create', [
            'title' => 'Nueva Gestion',
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    public function store(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->verifyCsrf();
        $errors = $this->validate($_POST, (int) $user['id_colegio']);
        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/gestiones/crear');
        }
        $this->gestiones->create((int) $user['id_colegio'], $_POST);
        flash('success', 'Gestion creada correctamente.');
        $this->redirect('/gestiones');
    }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $gestion = $this->gestiones->find($this->id(), (int) $user['id_colegio']);
        if ($gestion === null) { $this->notFound(); return; }
        $this->view('academic/gestiones/edit', [
            'title' => 'Editar Gestion',
            'gestion' => $gestion,
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->verifyCsrf();
        $id = $this->id();
        if ($this->gestiones->find($id, (int) $user['id_colegio']) === null) { $this->notFound(); return; }
        $errors = $this->validate($_POST, (int) $user['id_colegio'], $id);
        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/gestiones/editar?id=' . $id);
        }
        $this->gestiones->update($id, (int) $user['id_colegio'], $_POST);
        flash('success', 'Gestion actualizada correctamente.');
        $this->redirect('/gestiones');
    }

    private function validate(array $data, int $idColegio, ?int $ignoreId = null): array
    {
        $errors = [];
        $anio = (int) ($data['anio'] ?? 0);
        if ($anio < 2000 || $anio > 2100) { $errors[] = 'El anio debe estar entre 2000 y 2100.'; }
        if (trim($data['nombre'] ?? '') === '') { $errors[] = 'El nombre es obligatorio.'; }
        if ($this->gestiones->yearExists($idColegio, $anio, $ignoreId)) { $errors[] = 'Ya existe una gestion con ese anio.'; }
        return $errors;
    }

    private function id(): int
    {
        return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0));
    }
}
