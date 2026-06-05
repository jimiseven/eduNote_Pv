<?php
$filters = $filters ?? ['q' => '', 'estado' => ''];
$pagination = $pagination ?? ['total' => count($estudiantes ?? []), 'page' => 1, 'per_page' => 10, 'total_pages' => 1];
$page = (int) $pagination['page'];
$totalPages = (int) $pagination['total_pages'];
$queryForPage = function (int $targetPage) use ($filters): string {
    return http_build_query(array_filter([
        'q' => $filters['q'] ?? '',
        'estado' => $filters['estado'] ?? '',
        'page' => $targetPage,
    ], static fn ($value) => $value !== '' && $value !== null));
};
$estados = ['activo' => 'Activo', 'retirado' => 'Retirado', 'egresado' => 'Egresado', 'trasladado' => 'Trasladado', 'inactivo' => 'Inactivo'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Estudiantes</h2>
        <p class="text-muted mb-0">Registro estudiantil del colegio.</p>
    </div>
    <a href="<?= e(url('/estudiantes/crear')) ?>" class="btn btn-primary">Nuevo estudiante</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel-card p-3 mb-3">
    <form method="get" action="<?= e(url('/estudiantes')) ?>" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label" for="q">Buscar</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Nombre, RUDE o CI">
        </div>
        <div class="col-md-3">
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
            <a href="<?= e(url('/estudiantes')) ?>" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

<div class="panel-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Estudiante</th>
                    <th>RUDE</th>
                    <th>CI</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estudiantes)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay estudiantes para los filtros seleccionados.</td></tr>
                <?php endif; ?>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><strong><?= e(trim(($estudiante['apellido_paterno'] ?? '') . ' ' . ($estudiante['apellido_materno'] ?? '') . ' ' . $estudiante['nombres'])) ?></strong></td>
                        <td><code><?= e($estudiante['rude']) ?></code></td>
                        <td><?= e($estudiante['carnet_identidad'] ?? '-') ?></td>
                        <td><span class="badge text-bg-info"><?= e($estudiante['estado']) ?></span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-outline-primary btn-sm" href="<?= e(url('/estudiantes/ver?id=' . $estudiante['id_estudiante'])) ?>">Ver</a>
                                <a class="btn btn-outline-secondary btn-sm" href="<?= e(url('/estudiantes/editar?id=' . $estudiante['id_estudiante'])) ?>">Editar</a>
                                <a class="btn btn-outline-secondary btn-sm" href="<?= e(url('/estudiantes/responsables?id=' . $estudiante['id_estudiante'])) ?>">Responsables</a>
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
        Mostrando <?= e((string) count($estudiantes)) ?> de <?= e((string) $pagination['total']) ?> estudiantes.
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Paginacion de estudiantes">
            <ul class="pagination mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/estudiantes?' . $queryForPage($page - 1))) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= e(url('/estudiantes?' . $queryForPage($i))) ?>"><?= e((string) $i) ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/estudiantes?' . $queryForPage($page + 1))) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
