<?php
$nombreCompleto = trim(($estudiante['apellido_paterno'] ?? '') . ' ' . ($estudiante['apellido_materno'] ?? '') . ' ' . $estudiante['nombres']);
$edad = null;
if (!empty($estudiante['fecha_nacimiento'])) {
    $edad = (new DateTimeImmutable($estudiante['fecha_nacimiento']))->diff(new DateTimeImmutable())->y;
}
$estadoClass = match ($estudiante['estado']) {
    'activo' => 'text-bg-success',
    'inactivo' => 'text-bg-secondary',
    default => 'text-bg-info',
};
$complementarios = $complementarios ?? [];
$yesNo = static fn ($value): string => !empty($value) ? 'Si' : 'No';
?>

<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Ficha del estudiante</h2>
        <p class="text-muted mb-0">Informacion consolidada del registro estudiantil.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= e(url('/estudiantes')) ?>" class="btn btn-outline-secondary">Volver</a>
        <a href="<?= e(url('/estudiantes/editar?id=' . $estudiante['id_estudiante'])) ?>" class="btn btn-primary">Editar</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="panel-card h-100">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1"><?= e($nombreCompleto) ?></h3>
                    <div class="text-muted">RUDE: <code><?= e($estudiante['rude']) ?></code></div>
                </div>
                <span class="badge <?= e($estadoClass) ?>"><?= e($estudiante['estado']) ?></span>
            </div>

            <div class="row g-3">
                <div class="col-md-4"><div class="text-muted small">CI</div><div class="fw-semibold"><?= e($estudiante['carnet_identidad'] ?? '-') ?></div></div>
                <div class="col-md-4"><div class="text-muted small">Genero</div><div class="fw-semibold"><?= e($estudiante['genero'] ?? '-') ?></div></div>
                <div class="col-md-4"><div class="text-muted small">Fecha nacimiento</div><div class="fw-semibold"><?= e($estudiante['fecha_nacimiento'] ?? '-') ?><?= $edad !== null ? ' (' . e((string) $edad) . ' anos)' : '' ?></div></div>
                <div class="col-md-4"><div class="text-muted small">Pais</div><div class="fw-semibold"><?= e($estudiante['pais'] ?? '-') ?></div></div>
                <div class="col-md-4"><div class="text-muted small">Provincia/Departamento</div><div class="fw-semibold"><?= e($estudiante['provincia_departamento'] ?? '-') ?></div></div>
                <div class="col-md-4"><div class="text-muted small">Registrado</div><div class="fw-semibold"><?= e($estudiante['creado_en'] ?? '-') ?></div></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel-card h-100">
            <h3 class="h5 fw-bold mb-3">Resumen</h3>
            <div class="d-grid gap-3">
                <div><div class="text-muted small">Responsables</div><div class="h4 mb-0"><?= e((string) count($responsables)) ?></div></div>
                <div><div class="text-muted small">Matriculas</div><div class="h4 mb-0"><?= e((string) count($matriculas)) ?></div></div>
                <div><div class="text-muted small">Notas registradas</div><div class="h4 mb-0"><?= e((string) count($notas)) ?></div></div>
                <div><div class="text-muted small">Promedio simple</div><div class="h4 mb-0"><?= $promedio === null ? '-' : e(number_format((float) $promedio, 2)) ?></div></div>
            </div>
        </div>
    </div>
</div>

<div class="panel-card p-0 overflow-hidden mb-4">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h3 class="h5 fw-bold mb-0">Datos complementarios</h3>
        <a href="<?= e(url('/estudiantes/complementarios?id=' . $estudiante['id_estudiante'])) ?>" class="btn btn-outline-primary btn-sm">Editar datos</a>
    </div>
    <div class="p-3">
        <div class="row g-3">
            <div class="col-md-4"><div class="text-muted small">Direccion</div><div class="fw-semibold"><?= e(trim(($complementarios['direccion']['zona'] ?? '') . ' ' . ($complementarios['direccion']['numero_vivienda'] ?? '')) ?: '-') ?></div><div class="small text-muted"><?= e($complementarios['direccion']['municipio'] ?? '-') ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Contacto familiar</div><div class="fw-semibold"><?= e($complementarios['direccion']['celular'] ?? $complementarios['direccion']['telefono'] ?? '-') ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Salud</div><div class="fw-semibold">Seguro: <?= e($yesNo($complementarios['salud']['tiene_seguro'] ?? 0)) ?></div><div class="small text-muted">Hospital: <?= e($yesNo($complementarios['salud']['acceso_hospital'] ?? 0)) ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Servicios</div><div class="fw-semibold">Internet: <?= e($yesNo($complementarios['servicios']['internet'] ?? 0)) ?></div><div class="small text-muted">Vivienda: <?= e($complementarios['servicios']['tipo_vivienda'] ?? '-') ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Transporte</div><div class="fw-semibold"><?= e($complementarios['transporte']['medio'] ?? '-') ?></div><div class="small text-muted"><?= e($complementarios['transporte']['tiempo_llegada'] ?? '-') ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Dificultades</div><div class="fw-semibold"><?= e($yesNo($complementarios['dificultades']['tiene_dificultad'] ?? 0)) ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Actividad laboral</div><div class="fw-semibold">Trabaja: <?= e($yesNo($complementarios['actividad_laboral']['trabajo'] ?? 0)) ?></div><div class="small text-muted"><?= e($complementarios['actividad_laboral']['actividad'] ?? '-') ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Idioma/Cultura</div><div class="fw-semibold"><?= e($complementarios['idioma_cultura']['idioma'] ?? '-') ?></div><div class="small text-muted"><?= e($complementarios['idioma_cultura']['cultura'] ?? '-') ?></div></div>
            <div class="col-md-4"><div class="text-muted small">Abandono</div><div class="fw-semibold"><?= e($yesNo($complementarios['abandono']['abandono'] ?? 0)) ?></div><div class="small text-muted"><?= e($complementarios['abandono']['motivo'] ?? '-') ?></div></div>
        </div>
    </div>
</div>

<div class="panel-card p-0 overflow-hidden mb-4">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h3 class="h5 fw-bold mb-0">Responsables</h3>
        <a href="<?= e(url('/estudiantes/responsables?id=' . $estudiante['id_estudiante'])) ?>" class="btn btn-outline-primary btn-sm">Gestionar</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>Nombre</th><th>Parentesco</th><th>CI</th><th>Celular</th><th>Opciones</th></tr></thead>
            <tbody>
                <?php if (empty($responsables)): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay responsables asociados.</td></tr><?php endif; ?>
                <?php foreach ($responsables as $responsable): ?>
                    <tr>
                        <td><strong><?= e(trim(($responsable['apellido_paterno'] ?? '') . ' ' . ($responsable['apellido_materno'] ?? '') . ' ' . $responsable['nombres'])) ?></strong></td>
                        <td><?= e($responsable['parentesco'] ?? '-') ?><?= (int) $responsable['es_principal'] === 1 ? ' - Principal' : '' ?></td>
                        <td><?= e($responsable['carnet_identidad'] ?? '-') ?></td>
                        <td><?= e($responsable['celular'] ?? '-') ?></td>
                        <td class="small text-muted"><?= (int) $responsable['vive_con_estudiante'] === 1 ? 'Vive con estudiante' : 'No convive' ?> - <?= (int) $responsable['autorizado_recoger'] === 1 ? 'Autorizado' : 'No autorizado' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel-card p-0 overflow-hidden mb-4">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h3 class="h5 fw-bold mb-0">Historial de matriculas</h3>
        <a href="<?= e(url('/matriculas/crear')) ?>" class="btn btn-outline-primary btn-sm">Nueva matricula</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>Gestion</th><th>Curso</th><th>Fecha</th><th>Estado</th><th>Observacion</th></tr></thead>
            <tbody>
                <?php if (empty($matriculas)): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay matriculas registradas.</td></tr><?php endif; ?>
                <?php foreach ($matriculas as $matricula): ?>
                    <tr>
                        <td><?= e($matricula['gestion']) ?></td>
                        <td><?= e($matricula['nombre_nivel'] . ' ' . $matricula['grado'] . ' ' . $matricula['paralelo']) ?></td>
                        <td><?= e($matricula['fecha_matricula']) ?></td>
                        <td><span class="badge text-bg-info"><?= e($matricula['estado']) ?></span></td>
                        <td><?= e($matricula['observacion'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel-card p-0 overflow-hidden">
    <div class="p-3 border-bottom">
        <h3 class="h5 fw-bold mb-0">Notas registradas</h3>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>Gestion</th><th>Curso/Materia</th><th>Periodo</th><th>Evaluacion</th><th>Nota</th><th>Comentario</th></tr></thead>
            <tbody>
                <?php if (empty($notas)): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay notas registradas.</td></tr><?php endif; ?>
                <?php foreach ($notas as $nota): ?>
                    <tr>
                        <td><?= e($nota['gestion']) ?></td>
                        <td><?= e($nota['nombre_nivel'] . ' ' . $nota['grado'] . ' ' . $nota['paralelo'] . ' - ' . $nota['nombre_materia']) ?></td>
                        <td><?= e($nota['periodo']) ?></td>
                        <td><strong><?= e($nota['evaluacion']) ?></strong><div class="small text-muted"><?= e($nota['tipo']) ?> <?= !empty($nota['fecha']) ? '- ' . e($nota['fecha']) : '' ?></div></td>
                        <td><span class="badge text-bg-primary"><?= e(number_format((float) $nota['nota'], 2)) ?></span></td>
                        <td><?= e($nota['comentario'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
