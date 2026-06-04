<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Colegio;

class ColegiosController extends Controller
{
    private Colegio $colegios;

    public function __construct()
    {
        $this->colegios = new Colegio();
    }

    public function index(): void
    {
        $this->requireAdminGeneral();

        $this->view('colegios/index', [
            'title' => 'Colegios',
            'colegios' => $this->colegios->all(),
            'success' => $this->pullFlash('success'),
        ]);
    }

    public function create(): void
    {
        $this->requireAdminGeneral();

        $this->view('colegios/create', [
            'title' => 'Nuevo Colegio',
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]);

        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    public function store(): void
    {
        $this->requireAdminGeneral();
        $this->verifyCsrf();

        $errors = $this->validate($_POST);

        if ($this->colegios->codeExists(strtoupper(trim($_POST['codigo'] ?? '')))) {
            $errors[] = 'El codigo del colegio ya existe.';
        }

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/colegios/crear');
        }

        $this->colegios->create($_POST);
        $_SESSION['success'] = 'Colegio creado correctamente.';
        $this->redirect('/colegios');
    }

    public function edit(): void
    {
        $this->requireAdminGeneral();

        $colegio = $this->colegios->find($this->idFromRequest());

        if ($colegio === null) {
            http_response_code(404);
            echo 'Colegio no encontrado.';
            return;
        }

        $this->view('colegios/edit', [
            'title' => 'Editar Colegio',
            'colegio' => $colegio,
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]);

        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    public function update(): void
    {
        $this->requireAdminGeneral();
        $this->verifyCsrf();

        $idColegio = $this->idFromRequest();
        $colegio = $this->colegios->find($idColegio);

        if ($colegio === null) {
            http_response_code(404);
            echo 'Colegio no encontrado.';
            return;
        }

        $errors = $this->validate($_POST);

        if ($this->colegios->codeExists(strtoupper(trim($_POST['codigo'] ?? '')), $idColegio)) {
            $errors[] = 'El codigo del colegio ya existe.';
        }

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/colegios/editar?id=' . $idColegio);
        }

        $this->colegios->update($idColegio, $_POST);
        $_SESSION['success'] = 'Colegio actualizado correctamente.';
        $this->redirect('/colegios');
    }

    public function toggle(): void
    {
        $this->requireAdminGeneral();
        $this->verifyCsrf();

        $this->colegios->toggleStatus($this->idFromRequest());
        $_SESSION['success'] = 'Estado del colegio actualizado.';
        $this->redirect('/colegios');
    }

    private function requireAdminGeneral(): void
    {
        $this->requireRole('Administrador General');
    }

    private function validate(array $data): array
    {
        $errors = [];
        $nombre = trim($data['nombre'] ?? '');
        $codigo = trim($data['codigo'] ?? '');
        $correo = trim($data['correo'] ?? '');

        if ($nombre === '') {
            $errors[] = 'El nombre del colegio es obligatorio.';
        }

        if ($codigo === '') {
            $errors[] = 'El codigo del colegio es obligatorio.';
        } elseif (!preg_match('/^[A-Za-z0-9_\-]+$/', $codigo)) {
            $errors[] = 'El codigo solo puede contener letras, numeros, guiones y guion bajo.';
        }

        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no tiene un formato valido.';
        }

        return $errors;
    }

    private function idFromRequest(): int
    {
        return max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0));
    }

    private function pullFlash(string $key): ?string
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);

        return $value;
    }
}
