<?php

namespace App\Models;

use App\Core\Model;

class EstudianteResponsable extends Model
{
    public function byEstudiante(int $idEstudiante, int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT er.*, r.nombres, r.apellido_paterno, r.apellido_materno, r.carnet_identidad, r.celular
             FROM estudiantes_responsables er
             INNER JOIN responsables r ON r.id_responsable = er.id_responsable
             WHERE er.id_estudiante = :id_estudiante AND er.id_colegio = :id_colegio
             ORDER BY er.es_principal DESC, r.apellido_paterno ASC, r.nombres ASC'
        );
        $statement->execute(['id_estudiante' => $idEstudiante, 'id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function create(int $idColegio, int $idEstudiante, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO estudiantes_responsables (id_colegio, id_estudiante, id_responsable, parentesco, es_principal, vive_con_estudiante, autorizado_recoger)
             VALUES (:id_colegio, :id_estudiante, :id_responsable, :parentesco, :es_principal, :vive_con_estudiante, :autorizado_recoger)'
        );
        $statement->execute([
            'id_colegio' => $idColegio,
            'id_estudiante' => $idEstudiante,
            'id_responsable' => (int) ($data['id_responsable'] ?? 0),
            'parentesco' => in_array($data['parentesco'] ?? '', ['Padre','Madre','Tutor','Otro'], true) ? $data['parentesco'] : null,
            'es_principal' => isset($data['es_principal']) ? 1 : 0,
            'vive_con_estudiante' => isset($data['vive_con_estudiante']) ? 1 : 0,
            'autorizado_recoger' => isset($data['autorizado_recoger']) ? 1 : 0,
        ]);
    }

    public function delete(int $idRelacion, int $idColegio): void
    {
        $statement = $this->db->prepare('DELETE FROM estudiantes_responsables WHERE id_estudiante_responsable = :id AND id_colegio = :id_colegio');
        $statement->execute(['id' => $idRelacion, 'id_colegio' => $idColegio]);
    }
}
