<?php

namespace App\Models;

use App\Core\Model;

class Matricula extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT mt.*, e.nombres, e.apellido_paterno, e.apellido_materno, e.rude, g.nombre AS gestion,
                    c.grado, c.paralelo, n.nombre_nivel
             FROM matriculas mt
             INNER JOIN estudiantes e ON e.id_estudiante = mt.id_estudiante
             INNER JOIN gestiones g ON g.id_gestion = mt.id_gestion
             INNER JOIN cursos c ON c.id_curso = mt.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE mt.id_colegio = :id_colegio
             ORDER BY g.anio DESC, n.orden ASC, c.grado ASC, c.paralelo ASC, e.apellido_paterno ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idMatricula, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM matriculas WHERE id_matricula = :id_matricula AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_matricula' => $idMatricula, 'id_colegio' => $idColegio]);
        $matricula = $statement->fetch();
        return $matricula ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO matriculas (id_colegio, id_estudiante, id_curso, id_gestion, fecha_matricula, estado, observacion)
             VALUES (:id_colegio, :id_estudiante, :id_curso, :id_gestion, :fecha_matricula, :estado, :observacion)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idMatricula, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_matricula'] = $idMatricula;
        $statement = $this->db->prepare(
            'UPDATE matriculas SET id_estudiante = :id_estudiante, id_curso = :id_curso, id_gestion = :id_gestion,
                 fecha_matricula = :fecha_matricula, estado = :estado, observacion = :observacion
             WHERE id_matricula = :id_matricula AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    public function studentHasGestion(int $idEstudiante, int $idGestion, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM matriculas WHERE id_estudiante = :id_estudiante AND id_gestion = :id_gestion';
        $params = ['id_estudiante' => $idEstudiante, 'id_gestion' => $idGestion];
        if ($ignoreId !== null) {
            $sql .= ' AND id_matricula <> :id_matricula';
            $params['id_matricula'] = $ignoreId;
        }
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'id_estudiante' => (int) ($data['id_estudiante'] ?? 0),
            'id_curso' => (int) ($data['id_curso'] ?? 0),
            'id_gestion' => (int) ($data['id_gestion'] ?? 0),
            'fecha_matricula' => trim($data['fecha_matricula'] ?? '') ?: date('Y-m-d'),
            'estado' => in_array($data['estado'] ?? '', ['activo','retirado','promovido','reprobado','trasladado'], true) ? $data['estado'] : 'activo',
            'observacion' => trim($data['observacion'] ?? '') ?: null,
        ];
    }
}
