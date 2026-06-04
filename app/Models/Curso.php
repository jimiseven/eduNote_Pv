<?php

namespace App\Models;

use App\Core\Model;

class Curso extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT c.*, n.nombre_nivel
             FROM cursos c
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE c.id_colegio = :id_colegio
             ORDER BY n.orden ASC, c.grado ASC, c.paralelo ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idCurso, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM cursos WHERE id_curso = :id_curso AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_curso' => $idCurso, 'id_colegio' => $idColegio]);
        $curso = $statement->fetch();
        return $curso ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO cursos (id_colegio, id_nivel, grado, paralelo, turno, estado)
             VALUES (:id_colegio, :id_nivel, :grado, :paralelo, :turno, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idCurso, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_curso'] = $idCurso;
        $statement = $this->db->prepare(
            'UPDATE cursos SET id_nivel = :id_nivel, grado = :grado, paralelo = :paralelo, turno = :turno, estado = :estado
             WHERE id_curso = :id_curso AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    public function toggleStatus(int $idCurso, int $idColegio): void
    {
        $statement = $this->db->prepare('UPDATE cursos SET estado = IF(estado = 1, 0, 1) WHERE id_curso = :id_curso AND id_colegio = :id_colegio');
        $statement->execute(['id_curso' => $idCurso, 'id_colegio' => $idColegio]);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'id_nivel' => (int) ($data['id_nivel'] ?? 0),
            'grado' => (int) ($data['grado'] ?? 0),
            'paralelo' => strtoupper(trim($data['paralelo'] ?? '')),
            'turno' => 'Manana',
            'estado' => isset($data['estado']) ? 1 : 0,
        ];
    }
}
