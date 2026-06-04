<?php

namespace App\Models;

use App\Core\Model;

class Materia extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT m.*, p.nombre_materia AS materia_padre
             FROM materias m
             LEFT JOIN materias p ON p.id_materia = m.materia_padre_id
             WHERE m.id_colegio = :id_colegio
             ORDER BY m.estado DESC, m.nombre_materia ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function active(int $idColegio): array
    {
        $statement = $this->db->prepare('SELECT * FROM materias WHERE id_colegio = :id_colegio AND estado = 1 ORDER BY nombre_materia ASC');
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idMateria, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM materias WHERE id_materia = :id_materia AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_materia' => $idMateria, 'id_colegio' => $idColegio]);
        $materia = $statement->fetch();
        return $materia ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO materias (id_colegio, nombre_materia, es_submateria, materia_padre_id, es_extra, estado)
             VALUES (:id_colegio, :nombre_materia, :es_submateria, :materia_padre_id, :es_extra, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idMateria, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_materia'] = $idMateria;
        $statement = $this->db->prepare(
            'UPDATE materias SET nombre_materia = :nombre_materia, es_submateria = :es_submateria,
                 materia_padre_id = :materia_padre_id, es_extra = :es_extra, estado = :estado
             WHERE id_materia = :id_materia AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    public function toggleStatus(int $idMateria, int $idColegio): void
    {
        $statement = $this->db->prepare('UPDATE materias SET estado = IF(estado = 1, 0, 1) WHERE id_materia = :id_materia AND id_colegio = :id_colegio');
        $statement->execute(['id_materia' => $idMateria, 'id_colegio' => $idColegio]);
    }

    private function payload(int $idColegio, array $data): array
    {
        $esSubmateria = isset($data['es_submateria']) ? 1 : 0;
        return [
            'id_colegio' => $idColegio,
            'nombre_materia' => trim($data['nombre_materia'] ?? ''),
            'es_submateria' => $esSubmateria,
            'materia_padre_id' => $esSubmateria && ($data['materia_padre_id'] ?? '') !== '' ? (int) $data['materia_padre_id'] : null,
            'es_extra' => isset($data['es_extra']) ? 1 : 0,
            'estado' => isset($data['estado']) ? 1 : 0,
        ];
    }
}
