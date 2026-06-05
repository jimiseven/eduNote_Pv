<?php

namespace App\Models;

use App\Core\Model;

class Responsable extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare('SELECT * FROM responsables WHERE id_colegio = :id_colegio ORDER BY apellido_paterno ASC, apellido_materno ASC, nombres ASC');
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function paginate(int $idColegio, array $filters, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        [$where, $params] = $this->filterSql($idColegio, $filters);

        $count = $this->db->prepare('SELECT COUNT(*) FROM responsables ' . $where);
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $statement = $this->db->prepare(
            'SELECT * FROM responsables ' . $where . '
             ORDER BY apellido_paterno ASC, apellido_materno ASC, nombres ASC
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

    public function find(int $idResponsable, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM responsables WHERE id_responsable = :id_responsable AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_responsable' => $idResponsable, 'id_colegio' => $idColegio]);
        $responsable = $statement->fetch();
        return $responsable ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO responsables (id_colegio, nombres, apellido_paterno, apellido_materno, carnet_identidad, fecha_nacimiento, grado_instruccion, idioma_frecuente, celular)
             VALUES (:id_colegio, :nombres, :apellido_paterno, :apellido_materno, :carnet_identidad, :fecha_nacimiento, :grado_instruccion, :idioma_frecuente, :celular)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idResponsable, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_responsable'] = $idResponsable;
        $statement = $this->db->prepare(
            'UPDATE responsables SET nombres = :nombres, apellido_paterno = :apellido_paterno, apellido_materno = :apellido_materno,
                 carnet_identidad = :carnet_identidad, fecha_nacimiento = :fecha_nacimiento, grado_instruccion = :grado_instruccion,
                 idioma_frecuente = :idioma_frecuente, celular = :celular
             WHERE id_responsable = :id_responsable AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'nombres' => trim($data['nombres'] ?? ''),
            'apellido_paterno' => $this->nullable($data['apellido_paterno'] ?? null),
            'apellido_materno' => $this->nullable($data['apellido_materno'] ?? null),
            'carnet_identidad' => $this->nullable($data['carnet_identidad'] ?? null),
            'fecha_nacimiento' => $this->nullable($data['fecha_nacimiento'] ?? null),
            'grado_instruccion' => in_array($data['grado_instruccion'] ?? '', ['Ninguno','Primaria','Secundaria','Tecnico','Universitario','Postgrado'], true) ? $data['grado_instruccion'] : null,
            'idioma_frecuente' => $this->nullable($data['idioma_frecuente'] ?? null),
            'celular' => $this->nullable($data['celular'] ?? null),
        ];
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function filterSql(int $idColegio, array $filters): array
    {
        $conditions = ['id_colegio = :id_colegio'];
        $params = ['id_colegio' => $idColegio];

        $search = trim($filters['q'] ?? '');
        if ($search !== '') {
            $conditions[] = '(nombres LIKE :search OR apellido_paterno LIKE :search OR apellido_materno LIKE :search OR carnet_identidad LIKE :search OR celular LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        return ['WHERE ' . implode(' AND ', $conditions), $params];
    }
}
