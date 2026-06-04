<?php

namespace App\Models;

use App\Core\Model;

class Colegio extends Model
{
    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT *
             FROM colegios
             ORDER BY estado DESC, nombre ASC'
        );

        return $statement->fetchAll();
    }

    public function find(int $idColegio): ?array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM colegios
             WHERE id_colegio = :id_colegio
             LIMIT 1'
        );
        $statement->execute(['id_colegio' => $idColegio]);
        $colegio = $statement->fetch();

        return $colegio ?: null;
    }

    public function active(): array
    {
        $statement = $this->db->query(
            'SELECT *
             FROM colegios
             WHERE estado = 1
             ORDER BY nombre ASC'
        );

        return $statement->fetchAll();
    }

    public function create(array $data): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO colegios (nombre, codigo, nit, telefono, correo, direccion, ciudad, departamento, pais, estado)
             VALUES (:nombre, :codigo, :nit, :telefono, :correo, :direccion, :ciudad, :departamento, :pais, :estado)'
        );

        $statement->execute($this->payload($data));
    }

    public function update(int $idColegio, array $data): void
    {
        $payload = $this->payload($data);
        $payload['id_colegio'] = $idColegio;

        $statement = $this->db->prepare(
            'UPDATE colegios
             SET nombre = :nombre,
                 codigo = :codigo,
                 nit = :nit,
                 telefono = :telefono,
                 correo = :correo,
                 direccion = :direccion,
                 ciudad = :ciudad,
                 departamento = :departamento,
                 pais = :pais,
                 estado = :estado
             WHERE id_colegio = :id_colegio'
        );

        $statement->execute($payload);
    }

    public function toggleStatus(int $idColegio): void
    {
        $statement = $this->db->prepare(
            'UPDATE colegios
             SET estado = IF(estado = 1, 0, 1)
             WHERE id_colegio = :id_colegio'
        );

        $statement->execute(['id_colegio' => $idColegio]);
    }

    public function codeExists(string $codigo, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM colegios WHERE codigo = :codigo';
        $params = ['codigo' => $codigo];

        if ($ignoreId !== null) {
            $sql .= ' AND id_colegio <> :id_colegio';
            $params['id_colegio'] = $ignoreId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    private function payload(array $data): array
    {
        return [
            'nombre' => trim($data['nombre'] ?? ''),
            'codigo' => strtoupper(trim($data['codigo'] ?? '')),
            'nit' => $this->nullable($data['nit'] ?? null),
            'telefono' => $this->nullable($data['telefono'] ?? null),
            'correo' => $this->nullable($data['correo'] ?? null),
            'direccion' => $this->nullable($data['direccion'] ?? null),
            'ciudad' => $this->nullable($data['ciudad'] ?? null),
            'departamento' => $this->nullable($data['departamento'] ?? null),
            'pais' => $this->nullable($data['pais'] ?? 'Bolivia') ?: 'Bolivia',
            'estado' => isset($data['estado']) ? 1 : 0,
        ];
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
