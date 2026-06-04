<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Colegio;
use App\Models\Personal;
use App\Models\Rol;

class PersonalController extends Controller
{
    private Personal $personal;
    private Rol $roles;
    private Colegio $colegios;

    public function __construct()
    {
        $this->personal = new Personal();
        $this->roles = new Rol();
        $this->colegios = new Colegio();
    }

    public function index(): void
    {
        $user = $this->requireManager();
        $idColegio = $this->isAdminGeneral($user) ? null : (int) $user['id_colegio'];

        $this->view('personal/index', [
            'title' => $this->isAdminGeneral($user) ? 'Administradores y Personal' : 'Personal del Colegio',
            'personal' => $this->personal->all($idColegio),
            'success' => $this->pullFlash('success'),
            'authUser' => $user,
        ]);
    }

    public function create(): void
    {
        $user = $this->requireManager();

        $this->view('personal/create', [
            'title' => 'Nuevo Usuario',
            'roles' => $this->roles->allowedFor($user['nombre_rol']),
            'colegios' => $this->colegios->active(),
            'authUser' => $user,
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]);

        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    public function store(): void
    {
        $user = $this->requireManager();
        $this->verifyCsrf();
        $data = $this->normalizedInput($user);
        $errors = $this->validate($data, true, $user);
        $data = $this->normalizeColegioByRole($data, $user);

        if ($this->personal->usuarioExists($data['usuario'])) {
            $errors[] = 'El usuario ya existe.';
        }

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/personal/crear');
        }

        $this->personal->create($data);
        $_SESSION['success'] = 'Usuario creado correctamente.';
        $this->redirect('/personal');
    }

    public function edit(): void
    {
        $user = $this->requireManager();
        $persona = $this->findAllowedPerson($this->idFromRequest(), $user);

        if ($persona === null) {
            http_response_code(404);
            echo 'Usuario no encontrado.';
            return;
        }

        $this->view('personal/edit', [
            'title' => 'Editar Usuario',
            'persona' => $persona,
            'roles' => $this->roles->allowedFor($user['nombre_rol']),
            'colegios' => $this->colegios->active(),
            'authUser' => $user,
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ]);

        unset($_SESSION['form_errors'], $_SESSION['old']);
    }

    public function update(): void
    {
        $user = $this->requireManager();
        $this->verifyCsrf();
        $idPersonal = $this->idFromRequest();
        $persona = $this->findAllowedPerson($idPersonal, $user);

        if ($persona === null) {
            http_response_code(404);
            echo 'Usuario no encontrado.';
            return;
        }

        $data = $this->normalizedInput($user);
        $errors = $this->validate($data, false, $user);
        $data = $this->normalizeColegioByRole($data, $user);

        if ($this->personal->usuarioExists($data['usuario'], $idPersonal)) {
            $errors[] = 'El usuario ya existe.';
        }

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/personal/editar?id=' . $idPersonal);
        }

        $this->personal->update($idPersonal, $data);
        $_SESSION['success'] = 'Usuario actualizado correctamente.';
        $this->redirect('/personal');
    }

    public function toggle(): void
    {
        $user = $this->requireManager();
        $this->verifyCsrf();
        $idPersonal = $this->idFromRequest();

        if ((int) $user['id_personal'] === $idPersonal) {
            $_SESSION['success'] = 'No puedes cambiar tu propio estado.';
            $this->redirect('/personal');
        }

        $persona = $this->findAllowedPerson($idPersonal, $user);

        if ($persona !== null) {
            $this->personal->toggleStatus($idPersonal);
            $_SESSION['success'] = 'Estado del usuario actualizado.';
        }

        $this->redirect('/personal');
    }

    private function requireManager(): array
    {
        return $this->requireRole(['Administrador General', 'Administrador Colegio']);
    }

    private function normalizedInput(array $user): array
    {
        $data = $_POST;

        if (!$this->isAdminGeneral($user)) {
            $data['id_colegio'] = $user['id_colegio'];
        }

        return $data;
    }

    private function validate(array $data, bool $requirePassword, array $user): array
    {
        $errors = [];
        $allowedRoleIds = array_map('intval', array_column($this->roles->allowedFor($user['nombre_rol']), 'id_rol'));

        if (trim($data['nombres'] ?? '') === '') {
            $errors[] = 'Los nombres son obligatorios.';
        }

        if (trim($data['apellidos'] ?? '') === '') {
            $errors[] = 'Los apellidos son obligatorios.';
        }

        if (trim($data['usuario'] ?? '') === '') {
            $errors[] = 'El usuario es obligatorio.';
        } elseif (!preg_match('/^[A-Za-z0-9_.\-]+$/', trim($data['usuario']))) {
            $errors[] = 'El usuario solo puede contener letras, numeros, punto, guion y guion bajo.';
        }

        if (!in_array((int) ($data['id_rol'] ?? 0), $allowedRoleIds, true)) {
            $errors[] = 'El rol seleccionado no es valido para tu usuario.';
        }

        $roleName = $this->roleNameById((int) ($data['id_rol'] ?? 0), $user);
        $idColegio = ($data['id_colegio'] ?? '') === '' ? null : (int) $data['id_colegio'];

        if ($roleName === 'Administrador General') {
            $data['id_colegio'] = null;
        } elseif ($idColegio === null || $idColegio <= 0) {
            $errors[] = 'Debes asignar un colegio para este rol.';
        }

        if ($requirePassword && trim($data['password'] ?? '') === '') {
            $errors[] = 'La contrasena es obligatoria.';
        }

        if (trim($data['password'] ?? '') !== '' && strlen($data['password']) < 6) {
            $errors[] = 'La contrasena debe tener al menos 6 caracteres.';
        }

        return $errors;
    }

    private function normalizeColegioByRole(array $data, array $user): array
    {
        $roleName = $this->roleNameById((int) ($data['id_rol'] ?? 0), $user);

        if ($roleName === 'Administrador General') {
            $data['id_colegio'] = '';
        }

        return $data;
    }

    private function roleNameById(int $idRol, array $user): ?string
    {
        foreach ($this->roles->allowedFor($user['nombre_rol']) as $role) {
            if ((int) $role['id_rol'] === $idRol) {
                return $role['nombre_rol'];
            }
        }

        return null;
    }

    private function findAllowedPerson(int $idPersonal, array $user): ?array
    {
        $persona = $this->personal->find($idPersonal);

        if ($persona === null) {
            return null;
        }

        if ($this->isAdminGeneral($user)) {
            return $persona;
        }

        return (int) ($persona['id_colegio'] ?? 0) === (int) $user['id_colegio'] ? $persona : null;
    }

    private function isAdminGeneral(array $user): bool
    {
        return ($user['nombre_rol'] ?? '') === 'Administrador General';
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
