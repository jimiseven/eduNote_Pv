<?php

namespace App\Models;

use App\Core\Model;

class EstudianteComplementario extends Model
{
    private array $tables = [
        'direccion' => ['table' => 'estudiante_direccion', 'fields' => ['departamento', 'provincia', 'municipio', 'localidad', 'comunidad', 'zona', 'numero_vivienda', 'telefono', 'celular']],
        'salud' => ['table' => 'estudiante_salud', 'fields' => ['tiene_seguro', 'acceso_posta', 'acceso_centro_salud', 'acceso_hospital'], 'boolean' => ['tiene_seguro', 'acceso_posta', 'acceso_centro_salud', 'acceso_hospital']],
        'servicios' => ['table' => 'estudiante_servicios', 'fields' => ['agua_caneria', 'bano', 'alcantarillado', 'internet', 'energia', 'recojo_basura', 'tipo_vivienda'], 'boolean' => ['agua_caneria', 'bano', 'alcantarillado', 'internet', 'energia', 'recojo_basura']],
        'transporte' => ['table' => 'estudiante_transporte', 'fields' => ['medio', 'tiempo_llegada']],
        'dificultades' => ['table' => 'estudiante_dificultades', 'fields' => ['tiene_dificultad', 'auditiva', 'visual', 'intelectual', 'fisico_motora', 'psiquica_mental', 'autista'], 'boolean' => ['tiene_dificultad']],
        'actividad_laboral' => ['table' => 'estudiante_actividad_laboral', 'fields' => ['trabajo', 'meses_trabajo', 'actividad', 'turno_manana', 'turno_tarde', 'turno_noche', 'frecuencia'], 'boolean' => ['trabajo', 'turno_manana', 'turno_tarde', 'turno_noche']],
        'idioma_cultura' => ['table' => 'estudiante_idioma_cultura', 'fields' => ['idioma', 'cultura']],
        'abandono' => ['table' => 'estudiante_abandono', 'fields' => ['abandono', 'motivo'], 'boolean' => ['abandono']],
    ];

    private array $allowed = [
        'tipo_vivienda' => ['alquilada', 'propia', 'cedida', 'anticretico'],
        'medio' => ['a_pie', 'vehiculo', 'fluvial', 'otro'],
        'tiempo_llegada' => ['menos_media_hora', 'mas_media_hora'],
        'auditiva' => ['ninguna', 'leve', 'grave', 'muy_grave', 'multiple'],
        'visual' => ['ninguna', 'leve', 'grave', 'muy_grave', 'multiple'],
        'intelectual' => ['ninguna', 'leve', 'grave', 'muy_grave', 'multiple'],
        'fisico_motora' => ['ninguna', 'leve', 'grave', 'muy_grave', 'multiple'],
        'psiquica_mental' => ['ninguna', 'leve', 'grave', 'muy_grave', 'multiple'],
        'autista' => ['ninguna', 'leve', 'grave', 'muy_grave', 'multiple'],
        'frecuencia' => ['todos_dias', 'dias_habiles', 'fin_de_semana', 'esporadico', 'dias_festivos', 'vacaciones'],
        'motivo' => ['trabajo', 'falta_dinero', 'otro'],
        'meses_trabajo' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
    ];

    public function all(int $idEstudiante): array
    {
        $data = [];

        foreach ($this->tables as $key => $config) {
            $statement = $this->db->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id_estudiante = :id_estudiante LIMIT 1');
            $statement->execute(['id_estudiante' => $idEstudiante]);
            $data[$key] = $statement->fetch() ?: [];
        }

        return $data;
    }

    public function save(int $idEstudiante, array $data): void
    {
        foreach ($this->tables as $key => $config) {
            $payload = $this->payload($idEstudiante, $data[$key] ?? [], $config);
            $fields = $config['fields'];
            $columns = array_merge(['id_estudiante'], $fields);
            $placeholders = array_map(static fn (string $field): string => ':' . $field, $columns);
            $updates = array_map(static fn (string $field): string => $field . ' = VALUES(' . $field . ')', $fields);

            $statement = $this->db->prepare(
                'INSERT INTO ' . $config['table'] . ' (' . implode(', ', $columns) . ')
                 VALUES (' . implode(', ', $placeholders) . ')
                 ON DUPLICATE KEY UPDATE ' . implode(', ', $updates)
            );
            $statement->execute($payload);
        }
    }

    private function payload(int $idEstudiante, array $data, array $config): array
    {
        $payload = ['id_estudiante' => $idEstudiante];
        $booleanFields = $config['boolean'] ?? [];

        foreach ($config['fields'] as $field) {
            if (in_array($field, $booleanFields, true)) {
                $payload[$field] = isset($data[$field]) ? 1 : 0;
                continue;
            }

            if ($field === 'meses_trabajo') {
                $months = array_values(array_filter((array) ($data[$field] ?? []), fn ($value): bool => is_string($value) && in_array($value, $this->allowed['meses_trabajo'], true)));
                $payload[$field] = $months === [] ? null : implode(',', $months);
                continue;
            }

            $value = trim((string) ($data[$field] ?? ''));
            if (isset($this->allowed[$field]) && !in_array($value, $this->allowed[$field], true)) {
                $value = '';
            }
            $payload[$field] = $value === '' ? null : $value;
        }

        return $payload;
    }
}
