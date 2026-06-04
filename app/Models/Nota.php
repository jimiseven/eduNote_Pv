<?php

namespace App\Models;

use App\Core\Model;

class Nota extends Model
{
    public function studentsForEvaluation(int $idColegio, array $evaluacion): array
    {
        $statement = $this->db->prepare(
            'SELECT mt.id_matricula, e.nombres, e.apellido_paterno, e.apellido_materno, e.rude, nt.id_nota, nt.nota, nt.comentario
             FROM matriculas mt
             INNER JOIN estudiantes e ON e.id_estudiante = mt.id_estudiante
             LEFT JOIN notas nt ON nt.id_matricula = mt.id_matricula AND nt.id_evaluacion = :id_evaluacion
             WHERE mt.id_colegio = :id_colegio
               AND mt.id_curso = :id_curso
               AND mt.id_gestion = :id_gestion
               AND mt.estado = :estado
             ORDER BY e.apellido_paterno ASC, e.apellido_materno ASC, e.nombres ASC'
        );
        $statement->execute([
            'id_evaluacion' => (int) $evaluacion['id_evaluacion'],
            'id_colegio' => $idColegio,
            'id_curso' => (int) $evaluacion['id_curso'],
            'id_gestion' => (int) $evaluacion['id_gestion'],
            'estado' => 'activo',
        ]);
        return $statement->fetchAll();
    }

    public function saveBulk(int $idColegio, int $idEvaluacion, array $notas, array $comentarios): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO notas (id_colegio, id_evaluacion, id_matricula, nota, comentario)
             VALUES (:id_colegio, :id_evaluacion, :id_matricula, :nota, :comentario)
             ON DUPLICATE KEY UPDATE nota = VALUES(nota), comentario = VALUES(comentario), actualizado_en = CURRENT_TIMESTAMP'
        );

        foreach ($notas as $idMatricula => $nota) {
            if ($nota === '') {
                continue;
            }

            $statement->execute([
                'id_colegio' => $idColegio,
                'id_evaluacion' => $idEvaluacion,
                'id_matricula' => (int) $idMatricula,
                'nota' => (float) $nota,
                'comentario' => trim($comentarios[$idMatricula] ?? '') ?: null,
            ]);
        }
    }
}
