<?php

namespace App\Models;

use App\Core\Model;

class ConfiguracionSistema extends Model
{
    public function findByColegio(int $idColegio): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM configuracion_sistema WHERE id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_colegio' => $idColegio]);
        $config = $statement->fetch();
        return $config ?: null;
    }

    public function save(int $idColegio, array $data): void
    {
        $config = $this->findByColegio($idColegio);
        $payload = [
            'id_colegio' => $idColegio,
            'id_gestion_actual' => ($data['id_gestion_actual'] ?? '') === '' ? null : (int) $data['id_gestion_actual'],
            'cantidad_periodos' => (int) ($data['cantidad_periodos'] ?? 3),
            'escala_nota_minima' => (float) ($data['escala_nota_minima'] ?? 0),
            'escala_nota_maxima' => (float) ($data['escala_nota_maxima'] ?? 100),
            'nota_aprobacion' => ($data['nota_aprobacion'] ?? '') === '' ? null : (float) $data['nota_aprobacion'],
        ];

        if ($config === null) {
            $statement = $this->db->prepare(
                'INSERT INTO configuracion_sistema (id_colegio, id_gestion_actual, cantidad_periodos, escala_nota_minima, escala_nota_maxima, nota_aprobacion)
                 VALUES (:id_colegio, :id_gestion_actual, :cantidad_periodos, :escala_nota_minima, :escala_nota_maxima, :nota_aprobacion)'
            );
        } else {
            $statement = $this->db->prepare(
                'UPDATE configuracion_sistema SET id_gestion_actual = :id_gestion_actual, cantidad_periodos = :cantidad_periodos,
                    escala_nota_minima = :escala_nota_minima, escala_nota_maxima = :escala_nota_maxima, nota_aprobacion = :nota_aprobacion
                 WHERE id_colegio = :id_colegio'
            );
        }

        $statement->execute($payload);
    }
}
