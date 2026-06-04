<?php

namespace App\Models;

use App\Core\Model;

class CursoMateria extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT cm.*, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel
             FROM cursos_materias cm
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE cm.id_colegio = :id_colegio
             ORDER BY n.orden ASC, c.grado ASC, c.paralelo ASC, m.nombre_materia ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function active(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT cm.*, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel
             FROM cursos_materias cm
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE cm.id_colegio = :id_colegio AND cm.estado = 1 AND c.estado = 1 AND m.estado = 1
             ORDER BY n.orden ASC, c.grado ASC, c.paralelo ASC, m.nombre_materia ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idCursoMateria, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM cursos_materias WHERE id_curso_materia = :id_curso_materia AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_curso_materia' => $idCursoMateria, 'id_colegio' => $idColegio]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare('INSERT INTO cursos_materias (id_colegio, id_curso, id_materia, estado) VALUES (:id_colegio, :id_curso, :id_materia, :estado)');
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idCursoMateria, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_curso_materia'] = $idCursoMateria;
        $statement = $this->db->prepare('UPDATE cursos_materias SET id_curso = :id_curso, id_materia = :id_materia, estado = :estado WHERE id_curso_materia = :id_curso_materia AND id_colegio = :id_colegio');
        $statement->execute($payload);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'id_curso' => (int) ($data['id_curso'] ?? 0),
            'id_materia' => (int) ($data['id_materia'] ?? 0),
            'estado' => isset($data['estado']) ? 1 : 0,
        ];
    }
}
