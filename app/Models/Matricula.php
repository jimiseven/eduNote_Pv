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

    public function paginate(int $idColegio, array $filters, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        [$where, $params] = $this->filterSql($idColegio, $filters);

        $count = $this->db->prepare(
            'SELECT COUNT(*)
             FROM matriculas mt
             INNER JOIN estudiantes e ON e.id_estudiante = mt.id_estudiante
             INNER JOIN gestiones g ON g.id_gestion = mt.id_gestion
             INNER JOIN cursos c ON c.id_curso = mt.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel ' . $where
        );
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $statement = $this->db->prepare(
            'SELECT mt.*, e.nombres, e.apellido_paterno, e.apellido_materno, e.rude, g.nombre AS gestion,
                    c.grado, c.paralelo, n.nombre_nivel
             FROM matriculas mt
             INNER JOIN estudiantes e ON e.id_estudiante = mt.id_estudiante
             INNER JOIN gestiones g ON g.id_gestion = mt.id_gestion
             INNER JOIN cursos c ON c.id_curso = mt.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel ' . $where . '
             ORDER BY g.anio DESC, n.orden ASC, c.grado ASC, c.paralelo ASC, e.apellido_paterno ASC
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

    public function find(int $idMatricula, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM matriculas WHERE id_matricula = :id_matricula AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_matricula' => $idMatricula, 'id_colegio' => $idColegio]);
        $matricula = $statement->fetch();
        return $matricula ?: null;
    }

    public function byEstudiante(int $idEstudiante, int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT mt.*, g.nombre AS gestion, g.anio, c.grado, c.paralelo, n.nombre_nivel
             FROM matriculas mt
             INNER JOIN gestiones g ON g.id_gestion = mt.id_gestion
             INNER JOIN cursos c ON c.id_curso = mt.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE mt.id_estudiante = :id_estudiante AND mt.id_colegio = :id_colegio
             ORDER BY g.anio DESC, mt.fecha_matricula DESC'
        );
        $statement->execute(['id_estudiante' => $idEstudiante, 'id_colegio' => $idColegio]);
        return $statement->fetchAll();
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

    private function filterSql(int $idColegio, array $filters): array
    {
        $conditions = ['mt.id_colegio = :id_colegio'];
        $params = ['id_colegio' => $idColegio];

        $search = trim($filters['q'] ?? '');
        if ($search !== '') {
            $conditions[] = '(e.nombres LIKE :search OR e.apellido_paterno LIKE :search OR e.apellido_materno LIKE :search OR e.rude LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $idGestion = (int) ($filters['id_gestion'] ?? 0);
        if ($idGestion > 0) {
            $conditions[] = 'mt.id_gestion = :id_gestion';
            $params['id_gestion'] = $idGestion;
        }

        $estado = trim($filters['estado'] ?? '');
        if (in_array($estado, ['activo', 'retirado', 'promovido', 'reprobado', 'trasladado'], true)) {
            $conditions[] = 'mt.estado = :estado';
            $params['estado'] = $estado;
        }

        return ['WHERE ' . implode(' AND ', $conditions), $params];
    }
}
