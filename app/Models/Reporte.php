<?php

namespace App\Models;

use App\Core\Model;

class Reporte extends Model
{
    public function estudiantesPorCurso(int $idColegio, ?int $idGestion = null): array
    {
        $gestionSql = $idGestion !== null ? ' AND mt.id_gestion = :id_gestion' : '';
        $statement = $this->db->prepare(
            'SELECT g.nombre AS gestion, n.nombre_nivel, c.grado, c.paralelo, COUNT(mt.id_matricula) AS total
             FROM matriculas mt
             INNER JOIN gestiones g ON g.id_gestion = mt.id_gestion
             INNER JOIN cursos c ON c.id_curso = mt.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE mt.id_colegio = :id_colegio' . $gestionSql . '
             GROUP BY g.id_gestion, c.id_curso
             ORDER BY g.anio DESC, n.orden ASC, c.grado ASC, c.paralelo ASC'
        );
        $params = ['id_colegio' => $idColegio];
        if ($idGestion !== null) { $params['id_gestion'] = $idGestion; }
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function docentesPorMateria(int $idColegio, ?int $idGestion = null): array
    {
        $gestionSql = $idGestion !== null ? ' AND ad.id_gestion = :id_gestion' : '';
        $statement = $this->db->prepare(
            'SELECT g.nombre AS gestion, p.nombres, p.apellidos, m.nombre_materia, n.nombre_nivel, c.grado, c.paralelo, ad.estado
             FROM asignaciones_docentes ad
             INNER JOIN gestiones g ON g.id_gestion = ad.id_gestion
             INNER JOIN personal p ON p.id_personal = ad.id_personal
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             WHERE ad.id_colegio = :id_colegio' . $gestionSql . '
             ORDER BY g.anio DESC, p.apellidos ASC, m.nombre_materia ASC'
        );
        $params = ['id_colegio' => $idColegio];
        if ($idGestion !== null) { $params['id_gestion'] = $idGestion; }
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function responsablesPorEstudiante(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT e.nombres AS estudiante_nombres, e.apellido_paterno AS estudiante_paterno, e.apellido_materno AS estudiante_materno, e.rude,
                    r.nombres, r.apellido_paterno, r.apellido_materno, r.celular, er.parentesco, er.es_principal
             FROM estudiantes_responsables er
             INNER JOIN estudiantes e ON e.id_estudiante = er.id_estudiante
             INNER JOIN responsables r ON r.id_responsable = er.id_responsable
             WHERE er.id_colegio = :id_colegio
             ORDER BY e.apellido_paterno ASC, e.apellido_materno ASC, e.nombres ASC, er.es_principal DESC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function notasPorEvaluacion(int $idColegio, ?int $idGestion = null): array
    {
        $gestionSql = $idGestion !== null ? ' AND ad.id_gestion = :id_gestion' : '';
        $statement = $this->db->prepare(
            'SELECT ev.nombre AS evaluacion, ev.tipo, pa.nombre AS periodo, m.nombre_materia, n.nombre_nivel, c.grado, c.paralelo,
                    e.nombres, e.apellido_paterno, e.apellido_materno, e.rude, nt.nota
             FROM notas nt
             INNER JOIN evaluaciones ev ON ev.id_evaluacion = nt.id_evaluacion
             INNER JOIN periodos_academicos pa ON pa.id_periodo = ev.id_periodo
             INNER JOIN asignaciones_docentes ad ON ad.id_asignacion = ev.id_asignacion
             INNER JOIN cursos_materias cm ON cm.id_curso_materia = ad.id_curso_materia
             INNER JOIN materias m ON m.id_materia = cm.id_materia
             INNER JOIN cursos c ON c.id_curso = cm.id_curso
             INNER JOIN niveles n ON n.id_nivel = c.id_nivel
             INNER JOIN matriculas mt ON mt.id_matricula = nt.id_matricula
             INNER JOIN estudiantes e ON e.id_estudiante = mt.id_estudiante
             WHERE nt.id_colegio = :id_colegio' . $gestionSql . '
             ORDER BY pa.numero_periodo ASC, ev.nombre ASC, e.apellido_paterno ASC'
        );
        $params = ['id_colegio' => $idColegio];
        if ($idGestion !== null) { $params['id_gestion'] = $idGestion; }
        $statement->execute($params);
        return $statement->fetchAll();
    }
}
