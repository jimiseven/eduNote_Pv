<?php

namespace App\Models;

use App\Core\Model;

class AsignacionDocente extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT ad.*, g.nombre AS gestion, p.nombres, p.apellidos, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel
             FROM asignaciones_docentes ad
             INNER JOIN gestiones g ON g.id_gestion = ad.id_gestion
             INNER JOIN personal p ON p.id_personal = ad.id_personal
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE ad.id_colegio = :id_colegio
             ORDER BY g.anio DESC, p.apellidos ASC, n.orden ASC, c.grado ASC, c.paralelo ASC'
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
             FROM asignaciones_docentes ad
             INNER JOIN gestiones g ON g.id_gestion = ad.id_gestion
             INNER JOIN personal p ON p.id_personal = ad.id_personal
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel ' . $where
        );
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $statement = $this->db->prepare(
            'SELECT ad.*, g.nombre AS gestion, p.nombres, p.apellidos, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel
             FROM asignaciones_docentes ad
             INNER JOIN gestiones g ON g.id_gestion = ad.id_gestion
             INNER JOIN personal p ON p.id_personal = ad.id_personal
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel ' . $where . '
             ORDER BY g.anio DESC, p.apellidos ASC, n.orden ASC, c.grado ASC, c.paralelo ASC
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

    public function find(int $idAsignacion, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM asignaciones_docentes WHERE id_asignacion = :id_asignacion AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_asignacion' => $idAsignacion, 'id_colegio' => $idColegio]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function findForTeacher(int $idAsignacion, int $idColegio, int $idPersonal): ?array
    {
        $statement = $this->db->prepare(
            'SELECT ad.*, g.nombre AS gestion, g.anio, cm.id_curso, cm.id_materia, m.nombre_materia,
                    c.grado, c.paralelo, n.nombre_nivel
             FROM asignaciones_docentes ad
             INNER JOIN gestiones g ON g.id_gestion = ad.id_gestion
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE ad.id_asignacion = :id_asignacion
               AND ad.id_colegio = :id_colegio
               AND ad.id_personal = :id_personal
             LIMIT 1'
        );
        $statement->execute(['id_asignacion' => $idAsignacion, 'id_colegio' => $idColegio, 'id_personal' => $idPersonal]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function byTeacher(int $idColegio, int $idPersonal): array
    {
        $statement = $this->db->prepare(
            'SELECT ad.*, g.nombre AS gestion, g.anio, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel
             FROM asignaciones_docentes ad
             INNER JOIN gestiones g ON g.id_gestion = ad.id_gestion
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE ad.id_colegio = :id_colegio
               AND ad.id_personal = :id_personal
               AND ad.estado = :estado
             ORDER BY g.anio DESC, n.orden ASC, c.grado ASC, c.paralelo ASC, m.nombre_materia ASC'
        );
        $statement->execute(['id_colegio' => $idColegio, 'id_personal' => $idPersonal, 'estado' => 'activo']);
        return $statement->fetchAll();
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO asignaciones_docentes (id_colegio, id_gestion, id_personal, id_curso_materia, estado)
             VALUES (:id_colegio, :id_gestion, :id_personal, :id_curso_materia, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idAsignacion, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_asignacion'] = $idAsignacion;
        $statement = $this->db->prepare(
            'UPDATE asignaciones_docentes SET id_gestion = :id_gestion, id_personal = :id_personal,
                 id_curso_materia = :id_curso_materia, estado = :estado
             WHERE id_asignacion = :id_asignacion AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'id_gestion' => (int) ($data['id_gestion'] ?? 0),
            'id_personal' => (int) ($data['id_personal'] ?? 0),
            'id_curso_materia' => (int) ($data['id_curso_materia'] ?? 0),
            'estado' => in_array($data['estado'] ?? '', ['activo', 'inactivo'], true) ? $data['estado'] : 'activo',
        ];
    }

    private function filterSql(int $idColegio, array $filters): array
    {
        $conditions = ['ad.id_colegio = :id_colegio'];
        $params = ['id_colegio' => $idColegio];

        $search = trim($filters['q'] ?? '');
        if ($search !== '') {
            $conditions[] = '(p.nombres LIKE :search OR p.apellidos LIKE :search OR m.nombre_materia LIKE :search OR n.nombre_nivel LIKE :search OR c.grado LIKE :search OR c.paralelo LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $idGestion = (int) ($filters['id_gestion'] ?? 0);
        if ($idGestion > 0) {
            $conditions[] = 'ad.id_gestion = :id_gestion';
            $params['id_gestion'] = $idGestion;
        }

        $estado = trim($filters['estado'] ?? '');
        if (in_array($estado, ['activo', 'inactivo'], true)) {
            $conditions[] = 'ad.estado = :estado';
            $params['estado'] = $estado;
        }

        return ['WHERE ' . implode(' AND ', $conditions), $params];
    }
}
