<?php

require_once BASE_PATH . '/app/Helpers/functions.php';

use App\Controllers\AuthController;
use App\Controllers\AccountController;
use App\Controllers\ColegiosController;
use App\Controllers\DashboardController;
use App\Controllers\PersonalController;
use App\Controllers\Academic\ConfiguracionController;
use App\Controllers\Academic\AsignacionesDocentesController;
use App\Controllers\Academic\CursosController;
use App\Controllers\Academic\CursosMateriasController;
use App\Controllers\Academic\GestionesController;
use App\Controllers\Academic\MateriasController;
use App\Controllers\Academic\NivelesController;
use App\Controllers\Academic\PeriodosController;
use App\Controllers\Students\EstudiantesController;
use App\Controllers\Students\MatriculasController;
use App\Controllers\Students\ResponsablesController;
use App\Controllers\Teacher\EvaluacionesController as ProfesorEvaluacionesController;
use App\Controllers\Teacher\NotasController as ProfesorNotasController;
use App\Controllers\Teacher\ProfesorController;
use App\Controllers\Reports\ReportesController;

$router->get('/', [AuthController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/cuenta/cambiar-contrasena', [AccountController::class, 'changePassword']);
$router->post('/cuenta/cambiar-contrasena', [AccountController::class, 'updatePassword']);

$router->get('/admin-general/dashboard', [DashboardController::class, 'adminGeneral']);
$router->get('/admin-colegio/dashboard', [DashboardController::class, 'adminColegio']);
$router->get('/director/dashboard', [DashboardController::class, 'director']);
$router->get('/secretario/dashboard', [DashboardController::class, 'secretario']);
$router->get('/profesor/dashboard', [DashboardController::class, 'profesor']);

$router->get('/colegios', [ColegiosController::class, 'index']);
$router->get('/colegios/crear', [ColegiosController::class, 'create']);
$router->post('/colegios', [ColegiosController::class, 'store']);
$router->get('/colegios/editar', [ColegiosController::class, 'edit']);
$router->post('/colegios/actualizar', [ColegiosController::class, 'update']);
$router->post('/colegios/cambiar-estado', [ColegiosController::class, 'toggle']);

$router->get('/personal', [PersonalController::class, 'index']);
$router->get('/personal/crear', [PersonalController::class, 'create']);
$router->post('/personal', [PersonalController::class, 'store']);
$router->get('/personal/editar', [PersonalController::class, 'edit']);
$router->post('/personal/actualizar', [PersonalController::class, 'update']);
$router->post('/personal/cambiar-estado', [PersonalController::class, 'toggle']);

$router->get('/gestiones', [GestionesController::class, 'index']);
$router->get('/gestiones/crear', [GestionesController::class, 'create']);
$router->post('/gestiones', [GestionesController::class, 'store']);
$router->get('/gestiones/editar', [GestionesController::class, 'edit']);
$router->post('/gestiones/actualizar', [GestionesController::class, 'update']);

$router->get('/periodos', [PeriodosController::class, 'index']);
$router->get('/periodos/crear', [PeriodosController::class, 'create']);
$router->post('/periodos', [PeriodosController::class, 'store']);
$router->get('/periodos/editar', [PeriodosController::class, 'edit']);
$router->post('/periodos/actualizar', [PeriodosController::class, 'update']);

$router->get('/niveles', [NivelesController::class, 'index']);
$router->get('/niveles/crear', [NivelesController::class, 'create']);
$router->post('/niveles', [NivelesController::class, 'store']);
$router->get('/niveles/editar', [NivelesController::class, 'edit']);
$router->post('/niveles/actualizar', [NivelesController::class, 'update']);

$router->get('/cursos', [CursosController::class, 'index']);
$router->get('/cursos/crear', [CursosController::class, 'create']);
$router->post('/cursos', [CursosController::class, 'store']);
$router->get('/cursos/editar', [CursosController::class, 'edit']);
$router->post('/cursos/actualizar', [CursosController::class, 'update']);
$router->post('/cursos/cambiar-estado', [CursosController::class, 'toggle']);

$router->get('/configuracion-academica', [ConfiguracionController::class, 'edit']);
$router->post('/configuracion-academica', [ConfiguracionController::class, 'update']);

$router->get('/materias', [MateriasController::class, 'index']);
$router->get('/materias/crear', [MateriasController::class, 'create']);
$router->post('/materias', [MateriasController::class, 'store']);
$router->get('/materias/editar', [MateriasController::class, 'edit']);
$router->post('/materias/actualizar', [MateriasController::class, 'update']);
$router->post('/materias/cambiar-estado', [MateriasController::class, 'toggle']);

$router->get('/cursos-materias', [CursosMateriasController::class, 'index']);
$router->get('/cursos-materias/crear', [CursosMateriasController::class, 'create']);
$router->post('/cursos-materias', [CursosMateriasController::class, 'store']);
$router->get('/cursos-materias/editar', [CursosMateriasController::class, 'edit']);
$router->post('/cursos-materias/actualizar', [CursosMateriasController::class, 'update']);

$router->get('/asignaciones-docentes', [AsignacionesDocentesController::class, 'index']);
$router->get('/asignaciones-docentes/crear', [AsignacionesDocentesController::class, 'create']);
$router->post('/asignaciones-docentes', [AsignacionesDocentesController::class, 'store']);
$router->get('/asignaciones-docentes/editar', [AsignacionesDocentesController::class, 'edit']);
$router->post('/asignaciones-docentes/actualizar', [AsignacionesDocentesController::class, 'update']);

$router->get('/estudiantes', [EstudiantesController::class, 'index']);
$router->get('/estudiantes/crear', [EstudiantesController::class, 'create']);
$router->post('/estudiantes', [EstudiantesController::class, 'store']);
$router->get('/estudiantes/editar', [EstudiantesController::class, 'edit']);
$router->post('/estudiantes/actualizar', [EstudiantesController::class, 'update']);
$router->get('/estudiantes/responsables', [EstudiantesController::class, 'responsables']);
$router->post('/estudiantes/responsables', [EstudiantesController::class, 'storeResponsable']);
$router->post('/estudiantes/responsables/eliminar', [EstudiantesController::class, 'deleteResponsable']);

$router->get('/responsables', [ResponsablesController::class, 'index']);
$router->get('/responsables/crear', [ResponsablesController::class, 'create']);
$router->post('/responsables', [ResponsablesController::class, 'store']);
$router->get('/responsables/editar', [ResponsablesController::class, 'edit']);
$router->post('/responsables/actualizar', [ResponsablesController::class, 'update']);

$router->get('/matriculas', [MatriculasController::class, 'index']);
$router->get('/matriculas/crear', [MatriculasController::class, 'create']);
$router->post('/matriculas', [MatriculasController::class, 'store']);
$router->get('/matriculas/editar', [MatriculasController::class, 'edit']);
$router->post('/matriculas/actualizar', [MatriculasController::class, 'update']);

$router->get('/profesor/materias', [ProfesorController::class, 'materias']);
$router->get('/profesor/evaluaciones', [ProfesorController::class, 'evaluaciones']);
$router->get('/profesor/evaluaciones/crear', [ProfesorEvaluacionesController::class, 'create']);
$router->post('/profesor/evaluaciones', [ProfesorEvaluacionesController::class, 'store']);
$router->get('/profesor/evaluaciones/editar', [ProfesorEvaluacionesController::class, 'edit']);
$router->post('/profesor/evaluaciones/actualizar', [ProfesorEvaluacionesController::class, 'update']);
$router->get('/profesor/notas', [ProfesorNotasController::class, 'edit']);
$router->post('/profesor/notas', [ProfesorNotasController::class, 'update']);

$router->get('/reportes', [ReportesController::class, 'index']);
$router->get('/reportes/estudiantes-por-curso', [ReportesController::class, 'estudiantesPorCurso']);
$router->get('/reportes/docentes-por-materia', [ReportesController::class, 'docentesPorMateria']);
$router->get('/reportes/responsables-por-estudiante', [ReportesController::class, 'responsablesPorEstudiante']);
$router->get('/reportes/notas-por-evaluacion', [ReportesController::class, 'notasPorEvaluacion']);
