<?php

namespace App\Models;

use App\Core\Model;

class Personal extends Model
{
    public function all(?int $idColegio = null): array
    {
        $sql = 'SELECT p.*, r.nombre_rol, c.nombre AS colegio
                FROM personal p
                INNER JOIN roles r ON r.id_rol = p.id_rol
                LEFT JOIN colegios c ON c.id_colegio = p.id_colegio';
        $params = [];

        if ($idColegio !== null) {
            $sql .= ' WHERE p.id_colegio = :id_colegio';
            $params['id_colegio'] = $idColegio;
        }

        $sql .= ' ORDER BY p.estado DESC, p.apellidos ASC, p.nombres ASC';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function paginate(?int $idColegio, array $filters, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        [$where, $params] = $this->filterSql($idColegio, $filters);

        $count = $this->db->prepare(
            'SELECT COUNT(*)
             FROM personal p
             INNER JOIN roles r ON r.id_rol = p.id_rol
             LEFT JOIN colegios c ON c.id_colegio = p.id_colegio ' . $where
        );
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $statement = $this->db->prepare(
            'SELECT p.*, r.nombre_rol, c.nombre AS colegio
             FROM personal p
             INNER JOIN roles r ON r.id_rol = p.id_rol
             LEFT JOIN colegios c ON c.id_colegio = p.id_colegio ' . $where . '
             ORDER BY p.estado DESC, p.apellidos ASC, p.nombres ASC
             LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->execute();

        return [
            'data' => $statement->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ];
    }

    public function teachers(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT p.*
             FROM personal p
             INNER JOIN roles r ON r.id_rol = p.id_rol
             WHERE p.id_colegio = :id_colegio
               AND p.estado = 1
               AND r.nombre_rol = :rol
             ORDER BY p.apellidos ASC, p.nombres ASC'
        );
        $statement->execute(['id_colegio' => $idColegio, 'rol' => 'Profesor']);
        return $statement->fetchAll();
    }

    public function find(int $idPersonal): ?array
    {
        $statement = $this->db->prepare(
            'SELECT p.*, r.nombre_rol, c.nombre AS colegio
             FROM personal p
             INNER JOIN roles r ON r.id_rol = p.id_rol
             LEFT JOIN colegios c ON c.id_colegio = p.id_colegio
             WHERE p.id_personal = :id_personal
             LIMIT 1'
        );
        $statement->execute(['id_personal' => $idPersonal]);
        $personal = $statement->fetch();

        return $personal ?: null;
    }

    public function findActiveByUsuario(string $usuario): ?array
    {
        $statement = $this->db->prepare(
            'SELECT p.*, r.nombre_rol, c.nombre AS colegio
             FROM personal p
             INNER JOIN roles r ON r.id_rol = p.id_rol
             LEFT JOIN colegios c ON c.id_colegio = p.id_colegio
             WHERE p.usuario = :usuario
               AND p.estado = 1
             LIMIT 1'
        );

        $statement->execute(['usuario' => $usuario]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function updateLastAccess(int $idPersonal): void
    {
        $statement = $this->db->prepare(
            'UPDATE personal
             SET ultimo_acceso = NOW()
             WHERE id_personal = :id_personal'
        );

        $statement->execute(['id_personal' => $idPersonal]);
    }

    public function updatePassword(int $idPersonal, string $password, bool $mustChange = false): void
    {
        $statement = $this->db->prepare(
            'UPDATE personal
             SET password = :password,
                 debe_cambiar_password = :debe_cambiar_password
             WHERE id_personal = :id_personal'
        );

        $statement->execute([
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'debe_cambiar_password' => $mustChange ? 1 : 0,
            'id_personal' => $idPersonal,
        ]);
    }

    public function create(array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO personal (id_colegio, id_rol, nombres, apellidos, celular, carnet_identidad, usuario, password, estado, debe_cambiar_password)
             VALUES (:id_colegio, :id_rol, :nombres, :apellidos, :celular, :carnet_identidad, :usuario, :password, :estado, 1)'
        );

        $statement->execute($this->payload($data, true));
    }

    public function update(int $idPersonal, array $data): void
    {
        $payload = $this->payload($data, false);
        $payload['id_personal'] = $idPersonal;

        $passwordSql = '';
        if (trim($data['password'] ?? '') !== '') {
            $passwordSql = ', password = :password, debe_cambiar_password = 1';
            $payload['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $statement = $this->db->prepare(
            'UPDATE personal
             SET id_colegio = :id_colegio,
                 id_rol = :id_rol,
                 nombres = :nombres,
                 apellidos = :apellidos,
                 celular = :celular,
                 carnet_identidad = :carnet_identidad,
                 usuario = :usuario,
                 estado = :estado' . $passwordSql . '
             WHERE id_personal = :id_personal'
        );

        $statement->execute($payload);
    }

    public function toggleStatus(int $idPersonal): void
    {
        $statement = $this->db->prepare(
            'UPDATE personal
             SET estado = IF(estado = 1, 0, 1)
             WHERE id_personal = :id_personal'
        );

        $statement->execute(['id_personal' => $idPersonal]);
    }

    public function usuarioExists(string $usuario, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM personal WHERE usuario = :usuario';
        $params = ['usuario' => $usuario];

        if ($ignoreId !== null) {
            $sql .= ' AND id_personal <> :id_personal';
            $params['id_personal'] = $ignoreId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    private function payload(array $data, bool $includePassword): array
    {
        $payload = [
            'id_colegio' => ($data['id_colegio'] ?? '') === '' ? null : (int) $data['id_colegio'],
            'id_rol' => (int) ($data['id_rol'] ?? 0),
            'nombres' => trim($data['nombres'] ?? ''),
            'apellidos' => trim($data['apellidos'] ?? ''),
            'celular' => $this->nullable($data['celular'] ?? null),
            'carnet_identidad' => $this->nullable($data['carnet_identidad'] ?? null),
            'usuario' => trim($data['usuario'] ?? ''),
            'estado' => isset($data['estado']) ? 1 : 0,
        ];

        if ($includePassword) {
            $payload['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        return $payload;
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function filterSql(?int $idColegio, array $filters): array
    {
        $conditions = [];
        $params = [];

        if ($idColegio !== null) {
            $conditions[] = 'p.id_colegio = :id_colegio';
            $params['id_colegio'] = $idColegio;
        }

        $search = trim($filters['q'] ?? '');
        if ($search !== '') {
            $conditions[] = '(p.nombres LIKE :search OR p.apellidos LIKE :search OR p.usuario LIKE :search OR p.carnet_identidad LIKE :search OR p.celular LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $idRol = (int) ($filters['id_rol'] ?? 0);
        if ($idRol > 0) {
            $conditions[] = 'p.id_rol = :id_rol';
            $params['id_rol'] = $idRol;
        }

        $estado = trim((string) ($filters['estado'] ?? ''));
        if ($estado === '1' || $estado === '0') {
            $conditions[] = 'p.estado = :estado';
            $params['estado'] = (int) $estado;
        }

        return [$conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions), $params];
    }
}
