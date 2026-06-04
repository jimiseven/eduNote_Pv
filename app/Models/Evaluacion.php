<?php

namespace App\Models;

use App\Core\Model;

class Evaluacion extends Model
{
    public function byTeacher(int $idColegio, int $idPersonal): array
    {
        $statement = $this->db->prepare(
            'SELECT ev.*, pa.nombre AS periodo, ad.id_personal, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel
             FROM evaluaciones ev
             INNER JOIN asignaciones_docentes ad ON ad.id_asignacion = ev.id_asignacion
             INNER JOIN periodos_academicos pa ON pa.id_periodo = ev.id_periodo
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE ev.id_colegio = :id_colegio AND ad.id_personal = :id_personal
             ORDER BY ev.creado_en DESC'
        );
        $statement->execute(['id_colegio' => $idColegio, 'id_personal' => $idPersonal]);
        return $statement->fetchAll();
    }

    public function byAsignacion(int $idColegio, int $idAsignacion): array
    {
        $statement = $this->db->prepare(
            'SELECT ev.*, pa.nombre AS periodo
             FROM evaluaciones ev
             INNER JOIN periodos_academicos pa ON pa.id_periodo = ev.id_periodo
             WHERE ev.id_colegio = :id_colegio AND ev.id_asignacion = :id_asignacion
             ORDER BY pa.numero_periodo ASC, ev.fecha DESC, ev.id_evaluacion DESC'
        );
        $statement->execute(['id_colegio' => $idColegio, 'id_asignacion' => $idAsignacion]);
        return $statement->fetchAll();
    }

    public function findForTeacher(int $idEvaluacion, int $idColegio, int $idPersonal): ?array
    {
        $statement = $this->db->prepare(
            'SELECT ev.*, ad.id_personal, ad.id_gestion, ad.id_curso_materia, cm.id_curso, m.nombre_materia, c.grado, c.paralelo, n.nombre_nivel, pa.nombre AS periodo
             FROM evaluaciones ev
             INNER JOIN asignaciones_docentes ad ON ad.id_asignacion = ev.id_asignacion
             INNER JOIN periodos_academicos pa ON pa.id_periodo = ev.id_periodo
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE ev.id_evaluacion = :id_evaluacion
               AND ev.id_colegio = :id_colegio
               AND ad.id_personal = :id_personal
             LIMIT 1'
        );
        $statement->execute(['id_evaluacion' => $idEvaluacion, 'id_colegio' => $idColegio, 'id_personal' => $idPersonal]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO evaluaciones (id_colegio, id_asignacion, id_periodo, nombre, tipo, ponderacion, fecha, estado)
             VALUES (:id_colegio, :id_asignacion, :id_periodo, :nombre, :tipo, :ponderacion, :fecha, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idEvaluacion, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_evaluacion'] = $idEvaluacion;
        $statement = $this->db->prepare(
            'UPDATE evaluaciones SET id_asignacion = :id_asignacion, id_periodo = :id_periodo, nombre = :nombre,
                 tipo = :tipo, ponderacion = :ponderacion, fecha = :fecha, estado = :estado
             WHERE id_evaluacion = :id_evaluacion AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'id_asignacion' => (int) ($data['id_asignacion'] ?? 0),
            'id_periodo' => (int) ($data['id_periodo'] ?? 0),
            'nombre' => trim($data['nombre'] ?? ''),
            'tipo' => in_array($data['tipo'] ?? '', ['tarea','practica','examen','participacion','proyecto','otro'], true) ? $data['tipo'] : 'otro',
            'ponderacion' => (float) ($data['ponderacion'] ?? 100),
            'fecha' => trim($data['fecha'] ?? '') ?: null,
            'estado' => in_array($data['estado'] ?? '', ['abierta','cerrada'], true) ? $data['estado'] : 'abierta',
        ];
    }
}
