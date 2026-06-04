<?php

namespace App\Models;

use App\Core\Model;

class Gestion extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare('SELECT * FROM gestiones WHERE id_colegio = :id_colegio ORDER BY anio DESC');
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idGestion, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM gestiones WHERE id_gestion = :id_gestion AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_gestion' => $idGestion, 'id_colegio' => $idColegio]);
        $gestion = $statement->fetch();
        return $gestion ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO gestiones (id_colegio, anio, nombre, fecha_inicio, fecha_fin, estado)
             VALUES (:id_colegio, :anio, :nombre, :fecha_inicio, :fecha_fin, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idGestion, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_gestion'] = $idGestion;
        $statement = $this->db->prepare(
            'UPDATE gestiones SET anio = :anio, nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado
             WHERE id_gestion = :id_gestion AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    public function yearExists(int $idColegio, int $anio, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM gestiones WHERE id_colegio = :id_colegio AND anio = :anio';
        $params = ['id_colegio' => $idColegio, 'anio' => $anio];
        if ($ignoreId !== null) {
            $sql .= ' AND id_gestion <> :id_gestion';
            $params['id_gestion'] = $ignoreId;
        }
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'anio' => (int) ($data['anio'] ?? 0),
            'nombre' => trim($data['nombre'] ?? ''),
            'fecha_inicio' => $this->nullableDate($data['fecha_inicio'] ?? null),
            'fecha_fin' => $this->nullableDate($data['fecha_fin'] ?? null),
            'estado' => in_array($data['estado'] ?? '', ['planificada', 'activa', 'cerrada'], true) ? $data['estado'] : 'planificada',
        ];
    }

    private function nullableDate(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
