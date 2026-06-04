-- Base de datos: coldbend
-- Esquema multi-colegio para administracion academica estudiantil
-- Compatible con MariaDB/MySQL, XAMPP/phpMyAdmin y cPanel

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `coldbend`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `coldbend`;

-- --------------------------------------------------------
-- Tablas principales de seguridad y multi-colegio
-- --------------------------------------------------------

CREATE TABLE `colegios` (
  `id_colegio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nit` varchar(30) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `pais` varchar(80) NOT NULL DEFAULT 'Bolivia',
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_colegio`),
  UNIQUE KEY `uk_colegios_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(60) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `uk_roles_nombre` (`nombre_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `personal` (
  `id_personal` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) DEFAULT NULL COMMENT 'NULL permitido para Administrador General',
  `id_rol` int(11) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `carnet_identidad` varchar(30) DEFAULT NULL,
  `usuario` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=habilitado, 0=inhabilitado',
  `ultimo_acceso` datetime DEFAULT NULL,
  `debe_cambiar_password` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_personal`),
  UNIQUE KEY `uk_personal_usuario` (`usuario`),
  UNIQUE KEY `uk_personal_colegio_ci` (`id_colegio`, `carnet_identidad`),
  KEY `idx_personal_colegio` (`id_colegio`),
  KEY `idx_personal_rol` (`id_rol`),
  CONSTRAINT `fk_personal_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_personal_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Gestion academica configurable por colegio
-- --------------------------------------------------------

CREATE TABLE `gestiones` (
  `id_gestion` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `anio` int(4) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('planificada','activa','cerrada') NOT NULL DEFAULT 'planificada',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_gestion`),
  UNIQUE KEY `uk_gestiones_colegio_anio` (`id_colegio`, `anio`),
  CONSTRAINT `fk_gestiones_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `periodos_academicos` (
  `id_periodo` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_gestion` int(11) NOT NULL,
  `numero_periodo` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('pendiente','activo','cerrado') NOT NULL DEFAULT 'pendiente',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_periodo`),
  UNIQUE KEY `uk_periodos_gestion_numero` (`id_gestion`, `numero_periodo`),
  KEY `idx_periodos_colegio` (`id_colegio`),
  CONSTRAINT `fk_periodos_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_periodos_gestion` FOREIGN KEY (`id_gestion`) REFERENCES `gestiones` (`id_gestion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `configuracion_sistema` (
  `id_configuracion` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_gestion_actual` int(11) DEFAULT NULL,
  `cantidad_periodos` int(11) NOT NULL DEFAULT 3,
  `escala_nota_minima` decimal(5,2) NOT NULL DEFAULT 0.00,
  `escala_nota_maxima` decimal(5,2) NOT NULL DEFAULT 100.00,
  `nota_aprobacion` decimal(5,2) DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_configuracion`),
  UNIQUE KEY `uk_configuracion_colegio` (`id_colegio`),
  KEY `idx_configuracion_gestion` (`id_gestion_actual`),
  CONSTRAINT `fk_configuracion_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_configuracion_gestion` FOREIGN KEY (`id_gestion_actual`) REFERENCES `gestiones` (`id_gestion`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_configuracion_escala` CHECK (`escala_nota_minima` >= 0 AND `escala_nota_maxima` <= 100 AND `escala_nota_minima` < `escala_nota_maxima`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Cursos, estudiantes y responsables
-- --------------------------------------------------------

CREATE TABLE `niveles` (
  `id_nivel` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `nombre_nivel` varchar(80) NOT NULL COMMENT 'Ej: Inicial, Primaria, Secundaria',
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_nivel`),
  UNIQUE KEY `uk_niveles_colegio_nombre` (`id_colegio`, `nombre_nivel`),
  CONSTRAINT `fk_niveles_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `grado` int(11) NOT NULL COMMENT 'Numero del curso, ej: 1, 2, 3',
  `paralelo` varchar(5) NOT NULL COMMENT 'Ej: A, B, C',
  `turno` enum('Manana') NOT NULL DEFAULT 'Manana',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_curso`),
  UNIQUE KEY `uk_cursos_colegio_nivel_grado_paralelo` (`id_colegio`, `id_nivel`, `grado`, `paralelo`),
  KEY `idx_cursos_nivel` (`id_nivel`),
  CONSTRAINT `fk_cursos_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cursos_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id_nivel`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellido_paterno` varchar(255) DEFAULT NULL,
  `apellido_materno` varchar(255) DEFAULT NULL,
  `genero` enum('Masculino','Femenino') DEFAULT NULL,
  `rude` varchar(30) NOT NULL COMMENT 'Registro Unico de Estudiante',
  `carnet_identidad` varchar(30) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `pais` varchar(80) DEFAULT NULL,
  `provincia_departamento` varchar(100) DEFAULT NULL,
  `estado` enum('activo','retirado','egresado','trasladado','inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_estudiante`),
  UNIQUE KEY `uk_estudiantes_colegio_rude` (`id_colegio`, `rude`),
  KEY `idx_estudiantes_colegio` (`id_colegio`),
  KEY `idx_estudiantes_ci` (`carnet_identidad`),
  CONSTRAINT `fk_estudiantes_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `responsables` (
  `id_responsable` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellido_paterno` varchar(255) DEFAULT NULL,
  `apellido_materno` varchar(255) DEFAULT NULL,
  `carnet_identidad` varchar(30) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `grado_instruccion` enum('Ninguno','Primaria','Secundaria','Tecnico','Universitario','Postgrado') DEFAULT NULL,
  `idioma_frecuente` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_responsable`),
  UNIQUE KEY `uk_responsables_colegio_ci` (`id_colegio`, `carnet_identidad`),
  CONSTRAINT `fk_responsables_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiantes_responsables` (
  `id_estudiante_responsable` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `parentesco` enum('Padre','Madre','Tutor','Otro') DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `vive_con_estudiante` tinyint(1) NOT NULL DEFAULT 0,
  `autorizado_recoger` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_estudiante_responsable`),
  UNIQUE KEY `uk_estudiantes_responsables` (`id_estudiante`, `id_responsable`),
  KEY `idx_er_colegio` (`id_colegio`),
  KEY `idx_er_responsable` (`id_responsable`),
  CONSTRAINT `fk_er_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_er_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_er_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `responsables` (`id_responsable`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `matriculas` (
  `id_matricula` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_gestion` int(11) NOT NULL,
  `fecha_matricula` date NOT NULL,
  `estado` enum('activo','retirado','promovido','reprobado','trasladado') NOT NULL DEFAULT 'activo',
  `observacion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_matricula`),
  UNIQUE KEY `uk_matriculas_estudiante_gestion` (`id_estudiante`, `id_gestion`),
  KEY `idx_matriculas_colegio` (`id_colegio`),
  KEY `idx_matriculas_curso` (`id_curso`),
  KEY `idx_matriculas_gestion` (`id_gestion`),
  CONSTRAINT `fk_matriculas_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_matriculas_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_matriculas_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON UPDATE CASCADE,
  CONSTRAINT `fk_matriculas_gestion` FOREIGN KEY (`id_gestion`) REFERENCES `gestiones` (`id_gestion`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Materias, asignacion docente y notas
-- --------------------------------------------------------

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `nombre_materia` varchar(255) NOT NULL,
  `es_submateria` tinyint(1) NOT NULL DEFAULT 0,
  `materia_padre_id` int(11) DEFAULT NULL,
  `es_extra` tinyint(1) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_materia`),
  UNIQUE KEY `uk_materias_colegio_nombre` (`id_colegio`, `nombre_materia`),
  KEY `idx_materias_padre` (`materia_padre_id`),
  CONSTRAINT `fk_materias_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_materias_padre` FOREIGN KEY (`materia_padre_id`) REFERENCES `materias` (`id_materia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cursos_materias` (
  `id_curso_materia` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_curso_materia`),
  UNIQUE KEY `uk_cursos_materias` (`id_curso`, `id_materia`),
  KEY `idx_cm_colegio` (`id_colegio`),
  KEY `idx_cm_materia` (`id_materia`),
  CONSTRAINT `fk_cm_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cm_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cm_materia` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `asignaciones_docentes` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_gestion` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL COMMENT 'Docente',
  `id_curso_materia` int(11) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_asignacion`),
  UNIQUE KEY `uk_asignacion_docente` (`id_gestion`, `id_personal`, `id_curso_materia`),
  KEY `idx_ad_colegio` (`id_colegio`),
  KEY `idx_ad_curso_materia` (`id_curso_materia`),
  KEY `idx_ad_personal` (`id_personal`),
  CONSTRAINT `fk_ad_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ad_gestion` FOREIGN KEY (`id_gestion`) REFERENCES `gestiones` (`id_gestion`) ON UPDATE CASCADE,
  CONSTRAINT `fk_ad_personal` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ad_curso_materia` FOREIGN KEY (`id_curso_materia`) REFERENCES `cursos_materias` (`id_curso_materia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `evaluaciones` (
  `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_asignacion` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo` enum('tarea','practica','examen','participacion','proyecto','otro') NOT NULL DEFAULT 'otro',
  `ponderacion` decimal(5,2) NOT NULL DEFAULT 100.00,
  `fecha` date DEFAULT NULL,
  `estado` enum('abierta','cerrada') NOT NULL DEFAULT 'abierta',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_evaluacion`),
  KEY `idx_evaluaciones_colegio` (`id_colegio`),
  KEY `idx_evaluaciones_asignacion` (`id_asignacion`),
  KEY `idx_evaluaciones_periodo` (`id_periodo`),
  CONSTRAINT `fk_evaluaciones_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_evaluaciones_asignacion` FOREIGN KEY (`id_asignacion`) REFERENCES `asignaciones_docentes` (`id_asignacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_evaluaciones_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodos_academicos` (`id_periodo`) ON UPDATE CASCADE,
  CONSTRAINT `chk_evaluaciones_ponderacion` CHECK (`ponderacion` > 0 AND `ponderacion` <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notas` (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_evaluacion` int(11) NOT NULL,
  `id_matricula` int(11) NOT NULL,
  `nota` decimal(5,2) NOT NULL DEFAULT 0.00,
  `comentario` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_nota`),
  UNIQUE KEY `uk_notas_evaluacion_matricula` (`id_evaluacion`, `id_matricula`),
  KEY `idx_notas_colegio` (`id_colegio`),
  KEY `idx_notas_matricula` (`id_matricula`),
  CONSTRAINT `fk_notas_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_evaluacion` FOREIGN KEY (`id_evaluacion`) REFERENCES `evaluaciones` (`id_evaluacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matriculas` (`id_matricula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_notas_rango` CHECK (`nota` >= 0 AND `nota` <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `calificaciones_periodo` (
  `id_calificacion_periodo` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `id_matricula` int(11) NOT NULL,
  `id_curso_materia` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `calificacion` decimal(5,2) NOT NULL DEFAULT 0.00,
  `comentario` text DEFAULT NULL,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_calificacion_periodo`),
  UNIQUE KEY `uk_calificacion_periodo` (`id_matricula`, `id_curso_materia`, `id_periodo`),
  KEY `idx_cp_colegio` (`id_colegio`),
  KEY `idx_cp_curso_materia` (`id_curso_materia`),
  KEY `idx_cp_periodo` (`id_periodo`),
  CONSTRAINT `fk_cp_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cp_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matriculas` (`id_matricula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cp_curso_materia` FOREIGN KEY (`id_curso_materia`) REFERENCES `cursos_materias` (`id_curso_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cp_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodos_academicos` (`id_periodo`) ON UPDATE CASCADE,
  CONSTRAINT `chk_cp_rango` CHECK (`calificacion` >= 0 AND `calificacion` <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Datos complementarios actuales del estudiante
-- --------------------------------------------------------

CREATE TABLE `estudiante_direccion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `provincia` varchar(50) DEFAULT NULL,
  `municipio` varchar(50) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `comunidad` varchar(100) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `numero_vivienda` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_dir` (`id_estudiante`),
  CONSTRAINT `fk_direccion_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_salud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `tiene_seguro` tinyint(1) NOT NULL DEFAULT 0,
  `acceso_posta` tinyint(1) NOT NULL DEFAULT 0,
  `acceso_centro_salud` tinyint(1) NOT NULL DEFAULT 0,
  `acceso_hospital` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_salud` (`id_estudiante`),
  CONSTRAINT `fk_salud_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `agua_caneria` tinyint(1) DEFAULT 0,
  `bano` tinyint(1) DEFAULT 0,
  `alcantarillado` tinyint(1) DEFAULT 0,
  `internet` tinyint(1) DEFAULT 0,
  `energia` tinyint(1) DEFAULT 0,
  `recojo_basura` tinyint(1) DEFAULT 0,
  `tipo_vivienda` enum('alquilada','propia','cedida','anticretico') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_serv` (`id_estudiante`),
  CONSTRAINT `fk_servicios_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_transporte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `medio` enum('a_pie','vehiculo','fluvial','otro') DEFAULT NULL,
  `tiempo_llegada` enum('menos_media_hora','mas_media_hora') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_trans` (`id_estudiante`),
  CONSTRAINT `fk_transporte_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_dificultades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `tiene_dificultad` tinyint(1) DEFAULT 0,
  `auditiva` enum('ninguna','leve','grave','muy_grave','multiple') DEFAULT 'ninguna',
  `visual` enum('ninguna','leve','grave','muy_grave','multiple') DEFAULT 'ninguna',
  `intelectual` enum('ninguna','leve','grave','muy_grave','multiple') DEFAULT 'ninguna',
  `fisico_motora` enum('ninguna','leve','grave','muy_grave','multiple') DEFAULT 'ninguna',
  `psiquica_mental` enum('ninguna','leve','grave','muy_grave','multiple') DEFAULT 'ninguna',
  `autista` enum('ninguna','leve','grave','muy_grave','multiple') DEFAULT 'ninguna',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_dif` (`id_estudiante`),
  CONSTRAINT `fk_dificultades_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_actividad_laboral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `trabajo` tinyint(1) DEFAULT 0,
  `meses_trabajo` set('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre') DEFAULT NULL,
  `actividad` varchar(100) DEFAULT NULL,
  `turno_manana` tinyint(1) DEFAULT 0,
  `turno_tarde` tinyint(1) DEFAULT 0,
  `turno_noche` tinyint(1) DEFAULT 0,
  `frecuencia` enum('todos_dias','dias_habiles','fin_de_semana','esporadico','dias_festivos','vacaciones') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_trab` (`id_estudiante`),
  CONSTRAINT `fk_trabajo_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_idioma_cultura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `idioma` varchar(50) DEFAULT NULL,
  `cultura` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_idioma` (`id_estudiante`),
  CONSTRAINT `fk_idioma_cultura_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estudiante_abandono` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `abandono` tinyint(1) DEFAULT 0,
  `motivo` enum('trabajo','falta_dinero','otro') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_estudiante_abandono` (`id_estudiante`),
  CONSTRAINT `fk_abandono_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Comunicacion y reportes
-- --------------------------------------------------------

CREATE TABLE `anuncios` (
  `id_anuncio` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_anuncio`),
  KEY `idx_anuncios_colegio` (`id_colegio`),
  KEY `idx_anuncios_creado_por` (`creado_por`),
  CONSTRAINT `fk_anuncios_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_anuncios_personal` FOREIGN KEY (`creado_por`) REFERENCES `personal` (`id_personal`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reportes_guardados` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `id_colegio` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo_base` varchar(50) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_reporte`),
  KEY `idx_reportes_colegio` (`id_colegio`),
  KEY `idx_reportes_personal` (`id_personal`),
  CONSTRAINT `fk_reportes_colegio` FOREIGN KEY (`id_colegio`) REFERENCES `colegios` (`id_colegio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reportes_personal` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reportes_guardados_columnas` (
  `id_columna` int(11) NOT NULL AUTO_INCREMENT,
  `id_reporte` int(11) NOT NULL,
  `campo` varchar(100) NOT NULL,
  `alias_mostrar` varchar(100) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_columna`),
  KEY `idx_rgc_reporte` (`id_reporte`),
  CONSTRAINT `fk_rgc_reporte` FOREIGN KEY (`id_reporte`) REFERENCES `reportes_guardados` (`id_reporte`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reportes_guardados_filtros` (
  `id_filtro` int(11) NOT NULL AUTO_INCREMENT,
  `id_reporte` int(11) NOT NULL,
  `campo` varchar(100) NOT NULL,
  `operador` enum('=','<>','>','<','>=','<=','entre','contiene','in') NOT NULL DEFAULT '=',
  `valor1` varchar(255) NOT NULL,
  `valor2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_filtro`),
  KEY `idx_rgf_reporte` (`id_reporte`),
  CONSTRAINT `fk_rgf_reporte` FOREIGN KEY (`id_reporte`) REFERENCES `reportes_guardados` (`id_reporte`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Datos iniciales
-- --------------------------------------------------------

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador General', 'Administra todos los colegios y la configuracion global del sistema'),
(2, 'Administrador Colegio', 'Administra un colegio especifico'),
(3, 'Director', 'Consulta y gestiona informacion academica del colegio'),
(4, 'Secretario', 'Registra estudiantes, responsables y matriculas'),
(5, 'Profesor', 'Carga evaluaciones y notas de sus asignaciones');

INSERT INTO `colegios` (`id_colegio`, `nombre`, `codigo`, `telefono`, `correo`, `direccion`, `ciudad`, `departamento`, `pais`, `estado`) VALUES
(1, 'Colegio Demo', 'COLEGIO_DEMO', NULL, NULL, NULL, NULL, NULL, 'Bolivia', 1);

-- Contrasena temporal para ambos usuarios: admin123
-- Debe cambiarse al primer ingreso en produccion.
INSERT INTO `personal` (`id_personal`, `id_colegio`, `id_rol`, `nombres`, `apellidos`, `celular`, `carnet_identidad`, `usuario`, `password`, `estado`, `debe_cambiar_password`) VALUES
(1, NULL, 1, 'Administrador', 'General', NULL, NULL, 'superadmin', '$2y$10$vY8JE8jiRcMpe4ZOQT/FTue.tqal3.7.6CXcchCZisCN7uPGdOaG.', 1, 1),
(2, 1, 2, 'Administrador', 'Colegio Demo', NULL, NULL, 'admin_demo', '$2y$10$vY8JE8jiRcMpe4ZOQT/FTue.tqal3.7.6CXcchCZisCN7uPGdOaG.', 1, 1);

INSERT INTO `gestiones` (`id_gestion`, `id_colegio`, `anio`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 1, 2026, 'Gestion 2026', '2026-02-01', '2026-11-30', 'activa');

INSERT INTO `periodos_academicos` (`id_periodo`, `id_colegio`, `id_gestion`, `numero_periodo`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 1, 1, 1, 'Primer Periodo', '2026-02-01', '2026-04-30', 'activo'),
(2, 1, 1, 2, 'Segundo Periodo', '2026-05-01', '2026-08-31', 'pendiente'),
(3, 1, 1, 3, 'Tercer Periodo', '2026-09-01', '2026-11-30', 'pendiente');

INSERT INTO `configuracion_sistema` (`id_configuracion`, `id_colegio`, `id_gestion_actual`, `cantidad_periodos`, `escala_nota_minima`, `escala_nota_maxima`, `nota_aprobacion`) VALUES
(1, 1, 1, 3, 0.00, 100.00, 51.00);

INSERT INTO `niveles` (`id_nivel`, `id_colegio`, `nombre_nivel`, `orden`) VALUES
(1, 1, 'Inicial', 1),
(2, 1, 'Primaria', 2),
(3, 1, 'Secundaria', 3);

COMMIT;
