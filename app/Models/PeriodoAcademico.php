<?php

namespace App\Models;

use App\Core\Model;

class PeriodoAcademico extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare(
            'SELECT p.*, g.nombre AS gestion_nombre, g.anio
             FROM periodos_academicos p
             INNER JOIN gestiones g ON g.id_gestion = p.id_gestion
             WHERE p.id_colegio = :id_colegio
             ORDER BY g.anio DESC, p.numero_periodo ASC'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idPeriodo, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM periodos_academicos WHERE id_periodo = :id_periodo AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_periodo' => $idPeriodo, 'id_colegio' => $idColegio]);
        $periodo = $statement->fetch();
        return $periodo ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO periodos_academicos (id_colegio, id_gestion, numero_periodo, nombre, fecha_inicio, fecha_fin, estado)
             VALUES (:id_colegio, :id_gestion, :numero_periodo, :nombre, :fecha_inicio, :fecha_fin, :estado)'
        );
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idPeriodo, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_periodo'] = $idPeriodo;
        $statement = $this->db->prepare(
            'UPDATE periodos_academicos SET id_gestion = :id_gestion, numero_periodo = :numero_periodo, nombre = :nombre,
                 fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado
             WHERE id_periodo = :id_periodo AND id_colegio = :id_colegio'
        );
        $statement->execute($payload);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'id_gestion' => (int) ($data['id_gestion'] ?? 0),
            'numero_periodo' => (int) ($data['numero_periodo'] ?? 0),
            'nombre' => trim($data['nombre'] ?? ''),
            'fecha_inicio' => $this->nullableDate($data['fecha_inicio'] ?? null),
            'fecha_fin' => $this->nullableDate($data['fecha_fin'] ?? null),
            'estado' => in_array($data['estado'] ?? '', ['pendiente', 'activo', 'cerrado'], true) ? $data['estado'] : 'pendiente',
        ];
    }

    private function nullableDate(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
