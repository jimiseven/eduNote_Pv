<?php

namespace App\Models;

use App\Core\Model;

class Estudiante extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM estudiantes WHERE id_colegio = :id_colegio ORDER BY apellido_paterno ASC, apellido_materno ASC, nombres ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idEstudiante, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM estudiantes WHERE id_estudiante = :id_estudiante AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_estudiante' => $idEstudiante, 'id_colegio' => $idColegio]);
        $estudiante = $statement->fetch();
        return $estudiante ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO estudiantes (id_colegio, nombres, apellido_paterno, apellido_materno, genero, rude, carnet_identidad, fecha_nacimiento, pais, provincia_departamento, estado)
             VALUES (:id_colegio, :nombres, :apellido_paterno, :apellido_materno, :genero, :rude, :carnet_identidad, :fecha_nacimiento, :pais, :provincia_departamento, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idEstudiante, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_estudiante'] = $idEstudiante;
        $statement = $this->db->prepare(
            'UPDATE estudiantes SET nombres = :nombres, apellido_paterno = :apellido_paterno, apellido_materno = :apellido_materno,
                 genero = :genero, rude = :rude, carnet_identidad = :carnet_identidad, fecha_nacimiento = :fecha_nacimiento,
                 pais = :pais, provincia_departamento = :provincia_departamento, estado = :estado
             WHERE id_estudiante = :id_estudiante AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    public function rudeExists(int $idColegio, string $rude, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM estudiantes WHERE id_colegio = :id_colegio AND rude = :rude';
        $params = ['id_colegio' => $idColegio, 'rude' => $rude];
        if ($ignoreId !== null) {
            $sql .= ' AND id_estudiante <> :id_estudiante';
            $params['id_estudiante'] = $ignoreId;
        }
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'nombres' => trim($data['nombres'] ?? ''),
            'apellido_paterno' => $this->nullable($data['apellido_paterno'] ?? null),
            'apellido_materno' => $this->nullable($data['apellido_materno'] ?? null),
            'genero' => in_array($data['genero'] ?? '', ['Masculino', 'Femenino'], true) ? $data['genero'] : null,
            'rude' => trim($data['rude'] ?? ''),
            'carnet_identidad' => $this->nullable($data['carnet_identidad'] ?? null),
            'fecha_nacimiento' => $this->nullable($data['fecha_nacimiento'] ?? null),
            'pais' => $this->nullable($data['pais'] ?? null),
            'provincia_departamento' => $this->nullable($data['provincia_departamento'] ?? null),
            'estado' => in_array($data['estado'] ?? '', ['activo','retirado','egresado','trasladado','inactivo'], true) ? $data['estado'] : 'activo',
        ];
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
