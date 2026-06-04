<?php

namespace App\Models;

use App\Core\Model;

class Rol extends Model
{
    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT *
             FROM roles
             ORDER BY id_rol ASC'
        );

        return $statement->fetchAll();
    }

    public function allowedFor(string $role): array
    {
        $roles = $this->all();

        if ($role === 'Administrador General') {
            return $roles;
        }

        return array_values(array_filter($roles, static function (array $rol): bool {
            return in_array($rol['nombre_rol'], ['Director', 'Secretario', 'Profesor'], true);
        }));
    }
}
