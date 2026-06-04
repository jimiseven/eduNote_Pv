<?php

namespace App\Models;

use App\Core\Model;

class Nivel extends Model
{
    public function all(int $idColegio): array
    {
        $statement = $this->db->prepare('SELECT * FROM niveles WHERE id_colegio = :id_colegio ORDER BY orden ASC, nombre_nivel ASC');
        $statement->execute(['id_colegio' => $idColegio]);
        return $statement->fetchAll();
    }

    public function find(int $idNivel, int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM niveles WHERE id_nivel = :id_nivel AND id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_nivel' => $idNivel, 'id_colegio' => $idColegio]);
        $nivel = $statement->fetch();
        return $nivel ?: null;
    }

    public function create(int $idColegio, array $data): void
    {
        $statement = $this->db->prepare('INSERT INTO niveles (id_colegio, nombre_nivel, orden) VALUES (:id_colegio, :nombre_nivel, :orden)');
        $statement->execute($this->payload($idColegio, $data));
    }

    public function update(int $idNivel, int $idColegio, array $data): void
    {
        $payload = $this->payload($idColegio, $data);
        $payload['id_nivel'] = $idNivel;
        $statement = $this->db->prepare('UPDATE niveles SET nombre_nivel = :nombre_nivel, orden = :orden WHERE id_nivel = :id_nivel AND id_colegio = :id_colegio');
        $statement->execute($payload);
    }

    private function payload(int $idColegio, array $data): array
    {
        return [
            'id_colegio' => $idColegio,
            'nombre_nivel' => trim($data['nombre_nivel'] ?? ''),
            'orden' => ($data['orden'] ?? '') === '' ? null : (int) $data['orden'],
        ];
    }
}
