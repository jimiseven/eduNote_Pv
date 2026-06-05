<?php
$filters = $filters ?? ['q' => '', 'tipo' => '', 'estado' => ''];
$pagination = $pagination ?? ['total' => count($evaluaciones ?? []), 'page' => 1, 'per_page' => 10, 'total_pages' => 1];
$page = (int) $pagination['page'];
$totalPages = (int) $pagination['total_pages'];
$queryForPage = function (int $targetPage) use ($filters): string {
    return http_build_query(array_filter([
        'q' => $filters['q'] ?? '',
        'tipo' => $filters['tipo'] ?? '',
        'estado' => $filters['estado'] ?? '',
        'page' => $targetPage,
    ], static fn ($value) => $value !== '' && $value !== null));
};
$tipos = ['tarea' => 'Tarea', 'practica' => 'Practica', 'examen' => 'Examen', 'participacion' => 'Participacion', 'proyecto' => 'Proyecto', 'otro' => 'Otro'];
$estados = ['abierta' => 'Abierta', 'cerrada' => 'Cerrada'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Mis evaluaciones</h2>
        <p class="text-muted mb-0">Evaluaciones creadas para tus materias asignadas.</p>
    </div>
    <a href="<?= e(url('/profesor/evaluaciones/crear')) ?>" class="btn btn-primary">Nueva evaluacion</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel-card p-3 mb-3">
    <form method="get" action="<?= e(url('/profesor/evaluaciones')) ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="q">Buscar</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Evaluacion, materia o curso">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="tipo">Tipo</label>
            <select class="form-select" id="tipo" name="tipo">
                <option value="">Todos</option>
                <?php foreach ($tipos as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($filters['tipo'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($filters['estado'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
            <a href="<?= e(url('/profesor/evaluaciones')) ?>" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

<div class="panel-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Evaluacion</th>
                    <th>Curso/Materia</th>
                    <th>Periodo</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evaluaciones)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay evaluaciones para los filtros seleccionados.</td></tr>
                <?php endif; ?>
                <?php foreach ($evaluaciones as $evaluacion): ?>
                    <tr>
                        <td><strong><?= e($evaluacion['nombre']) ?></strong><div class="small text-muted"><?= e($evaluacion['fecha'] ?? '') ?></div></td>
                        <td><?= e($evaluacion['nombre_nivel'] . ' ' . $evaluacion['grado'] . ' ' . $evaluacion['paralelo'] . ' - ' . $evaluacion['nombre_materia']) ?></td>
                        <td><?= e($evaluacion['periodo']) ?></td>
                        <td><?= e($evaluacion['tipo']) ?></td>
                        <td><span class="badge <?= $evaluacion['estado'] === 'abierta' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= e($evaluacion['estado']) ?></span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-outline-primary btn-sm" href="<?= e(url('/profesor/notas?id=' . $evaluacion['id_evaluacion'])) ?>">Notas</a>
                                <a class="btn btn-outline-secondary btn-sm" href="<?= e(url('/profesor/evaluaciones/editar?id=' . $evaluacion['id_evaluacion'])) ?>">Editar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
    <div class="text-muted small">
        Mostrando <?= e((string) count($evaluaciones)) ?> de <?= e((string) $pagination['total']) ?> evaluaciones.
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Paginacion de evaluaciones">
            <ul class="pagination mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/profesor/evaluaciones?' . $queryForPage($page - 1))) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= e(url('/profesor/evaluaciones?' . $queryForPage($i))) ?>"><?= e((string) $i) ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/profesor/evaluaciones?' . $queryForPage($page + 1))) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
