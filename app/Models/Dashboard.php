<?php

namespace App\Models;

use App\Core\Model;

class Dashboard extends Model
{
    public function adminGeneral(): array
    {
        return [
            'colegios_activos' => $this->count('colegios', 'estado = 1'),
            'colegios_inactivos' => $this->count('colegios', 'estado = 0'),
            'usuarios_activos' => $this->count('personal', 'estado = 1'),
            'administradores_colegio' => $this->countBySql(
                'SELECT COUNT(*) FROM personal p INNER JOIN roles r ON r.id_rol = p.id_rol WHERE r.nombre_rol = :rol',
                ['rol' => 'Administrador Colegio']
            ),
        ];
    }

    public function adminColegio(int $idColegio): array
    {
        $idGestion = $this->gestionActual($idColegio);

        return [
            'gestion_actual' => $this->gestionActualNombre($idGestion),
            'estudiantes_activos' => $this->count('estudiantes', 'id_colegio = :id_colegio AND estado = :estado', ['id_colegio' => $idColegio, 'estado' => 'activo']),
            'docentes_activos' => $this->countBySql(
                'SELECT COUNT(*) FROM personal p INNER JOIN roles r ON r.id_rol = p.id_rol WHERE p.id_colegio = :id_colegio AND p.estado = 1 AND r.nombre_rol = :rol',
                ['id_colegio' => $idColegio, 'rol' => 'Profesor']
            ),
            'cursos_activos' => $this->count('cursos', 'id_colegio = :id_colegio AND estado = 1', ['id_colegio' => $idColegio]),
            'materias_activas' => $this->count('materias', 'id_colegio = :id_colegio AND estado = 1', ['id_colegio' => $idColegio]),
            'matriculas_gestion' => $idGestion ? $this->count('matriculas', 'id_colegio = :id_colegio AND id_gestion = :id_gestion', ['id_colegio' => $idColegio, 'id_gestion' => $idGestion]) : 0,
            'evaluaciones' => $this->count('evaluaciones', 'id_colegio = :id_colegio', ['id_colegio' => $idColegio]),
            'docentes_sin_asignacion' => $this->docentesSinAsignacion($idColegio, $idGestion),
        ];
    }

    public function secretario(int $idColegio): array
    {
        $idGestion = $this->gestionActual($idColegio);

        return [
            'gestion_actual' => $this->gestionActualNombre($idGestion),
            'estudiantes_activos' => $this->count('estudiantes', 'id_colegio = :id_colegio AND estado = :estado', ['id_colegio' => $idColegio, 'estado' => 'activo']),
            'estudiantes_sin_matricula' => $this->estudiantesSinMatricula($idColegio, $idGestion),
            'responsables' => $this->count('responsables', 'id_colegio = :id_colegio', ['id_colegio' => $idColegio]),
            'matriculas_activas' => $idGestion ? $this->count('matriculas', 'id_colegio = :id_colegio AND id_gestion = :id_gestion AND estado = :estado', ['id_colegio' => $idColegio, 'id_gestion' => $idGestion, 'estado' => 'activo']) : 0,
        ];
    }

    public function director(int $idColegio): array
    {
        $idGestion = $this->gestionActual($idColegio);

        return [
            'gestion_actual' => $this->gestionActualNombre($idGestion),
            'estudiantes_activos' => $this->count('estudiantes', 'id_colegio = :id_colegio AND estado = :estado', ['id_colegio' => $idColegio, 'estado' => 'activo']),
            'docentes_asignados' => $idGestion ? $this->countBySql('SELECT COUNT(DISTINCT id_personal) FROM asignaciones_docentes WHERE id_colegio = :id_colegio AND id_gestion = :id_gestion AND estado = :estado', ['id_colegio' => $idColegio, 'id_gestion' => $idGestion, 'estado' => 'activo']) : 0,
            'cursos_con_matriculas' => $idGestion ? $this->countBySql('SELECT COUNT(DISTINCT id_curso) FROM matriculas WHERE id_colegio = :id_colegio AND id_gestion = :id_gestion', ['id_colegio' => $idColegio, 'id_gestion' => $idGestion]) : 0,
            'evaluaciones_abiertas' => $this->count('evaluaciones', 'id_colegio = :id_colegio AND estado = :estado', ['id_colegio' => $idColegio, 'estado' => 'abierta']),
        ];
    }

    public function profesor(int $idColegio, int $idPersonal): array
    {
        return [
            'materias_asignadas' => $this->count('asignaciones_docentes', 'id_colegio = :id_colegio AND id_personal = :id_personal AND estado = :estado', ['id_colegio' => $idColegio, 'id_personal' => $idPersonal, 'estado' => 'activo']),
            'evaluaciones_abiertas' => $this->countBySql(
                'SELECT COUNT(*) FROM evaluaciones ev INNER JOIN asignaciones_docentes ad ON ad.id_asignacion = ev.id_asignacion WHERE ev.id_colegio = :id_colegio AND ad.id_personal = :id_personal AND ev.estado = :estado',
                ['id_colegio' => $idColegio, 'id_personal' => $idPersonal, 'estado' => 'abierta']
            ),
            'evaluaciones_cerradas' => $this->countBySql(
                'SELECT COUNT(*) FROM evaluaciones ev INNER JOIN asignaciones_docentes ad ON ad.id_asignacion = ev.id_asignacion WHERE ev.id_colegio = :id_colegio AND ad.id_personal = :id_personal AND ev.estado = :estado',
                ['id_colegio' => $idColegio, 'id_personal' => $idPersonal, 'estado' => 'cerrada']
            ),
            'notas_cargadas' => $this->countBySql(
                'SELECT COUNT(*) FROM notas nt INNER JOIN evaluaciones ev ON ev.id_evaluacion = nt.id_evaluacion INNER JOIN asignaciones_docentes ad ON ad.id_asignacion = ev.id_asignacion WHERE nt.id_colegio = :id_colegio AND ad.id_personal = :id_personal',
                ['id_colegio' => $idColegio, 'id_personal' => $idPersonal]
            ),
        ];
    }

    private function gestionActual(int $idColegio): ?int
    {
        $statement = $this->db->prepare('SELECT id_gestion_actual FROM configuracion_sistema WHERE id_colegio = :id_colegio LIMIT 1');
        $statement->execute(['id_colegio' => $idColegio]);
        $id = $statement->fetchColumn();

        return $id ? (int) $id : null;
    }

    private function gestionActualNombre(?int $idGestion): string
    {
        if ($idGestion === null) {
            return 'Sin gestion configurada';
        }

        $statement = $this->db->prepare('SELECT nombre FROM gestiones WHERE id_gestion = :id_gestion LIMIT 1');
        $statement->execute(['id_gestion' => $idGestion]);

        return (string) ($statement->fetchColumn() ?: 'Sin gestion configurada');
    }

    private function estudiantesSinMatricula(int $idColegio, ?int $idGestion): int
    {
        if ($idGestion === null) {
            return 0;
        }

        return $this->countBySql(
            'SELECT COUNT(*) FROM estudiantes e WHERE e.id_colegio = :id_colegio AND e.estado = :estado AND NOT EXISTS (SELECT 1 FROM matriculas mt WHERE mt.id_estudiante = e.id_estudiante AND mt.id_gestion = :id_gestion)',
            ['id_colegio' => $idColegio, 'estado' => 'activo', 'id_gestion' => $idGestion]
        );
    }

    private function docentesSinAsignacion(int $idColegio, ?int $idGestion): int
    {
        if ($idGestion === null) {
            return 0;
        }

        return $this->countBySql(
            'SELECT COUNT(*) FROM personal p INNER JOIN roles r ON r.id_rol = p.id_rol WHERE p.id_colegio = :id_colegio AND p.estado = 1 AND r.nombre_rol = :rol AND NOT EXISTS (SELECT 1 FROM asignaciones_docentes ad WHERE ad.id_personal = p.id_personal AND ad.id_gestion = :id_gestion AND ad.estado = :estado)',
            ['id_colegio' => $idColegio, 'rol' => 'Profesor', 'id_gestion' => $idGestion, 'estado' => 'activo']
        );
    }

    private function count(string $table, string $where = '1=1', array $params = []): int
    {
        return $this->countBySql("SELECT COUNT(*) FROM {$table} WHERE {$where}", $params);
    }

    private function countBySql(string $sql, array $params = []): int
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }
}
