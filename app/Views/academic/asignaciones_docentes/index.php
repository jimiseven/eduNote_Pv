<?php
$filters = $filters ?? ['q' => '', 'id_gestion' => 0, 'estado' => ''];
$pagination = $pagination ?? ['total' => count($asignaciones ?? []), 'page' => 1, 'per_page' => 10, 'total_pages' => 1];
$page = (int) $pagination['page'];
$totalPages = (int) $pagination['total_pages'];
$queryForPage = function (int $targetPage) use ($filters): string {
    return http_build_query(array_filter([
        'q' => $filters['q'] ?? '',
        'id_gestion' => (int) ($filters['id_gestion'] ?? 0) ?: '',
        'estado' => $filters['estado'] ?? '',
        'page' => $targetPage,
    ], static fn ($value) => $value !== '' && $value !== null));
};
$estados = ['activo' => 'Activo', 'inactivo' => 'Inactivo'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Asignaciones docentes</h2>
        <p class="text-muted mb-0">Asigna docentes a materias por curso y gestion.</p>
    </div>
    <a href="<?= e(url('/asignaciones-docentes/crear')) ?>" class="btn btn-primary">Nueva asignacion</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel-card p-3 mb-3">
    <form method="get" action="<?= e(url('/asignaciones-docentes')) ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="q">Buscar</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Docente, materia o curso">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="id_gestion">Gestion</label>
            <select class="form-select" id="id_gestion" name="id_gestion">
                <option value="">Todas</option>
                <?php foreach (($gestiones ?? []) as $gestion): ?>
                    <option value="<?= e((string) $gestion['id_gestion']) ?>" <?= (int) ($filters['id_gestion'] ?? 0) === (int) $gestion['id_gestion'] ? 'selected' : '' ?>><?= e($gestion['nombre']) ?></option>
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
            <a href="<?= e(url('/asignaciones-docentes')) ?>" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

<div class="panel-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Gestion</th>
                    <th>Docente</th>
                    <th>Curso</th>
                    <th>Materia</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asignaciones)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay asignaciones para los filtros seleccionados.</td></tr>
                <?php endif; ?>
                <?php foreach ($asignaciones as $asignacion): ?>
                    <tr>
                        <td><?= e($asignacion['gestion']) ?></td>
                        <td><strong><?= e($asignacion['apellidos'] . ' ' . $asignacion['nombres']) ?></strong></td>
                        <td><?= e($asignacion['nombre_nivel'] . ' ' . $asignacion['grado'] . ' ' . $asignacion['paralelo']) ?></td>
                        <td><?= e($asignacion['nombre_materia']) ?></td>
                        <td><span class="badge <?= $asignacion['estado'] === 'activo' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= e($asignacion['estado']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= e(url('/asignaciones-docentes/editar?id=' . $asignacion['id_asignacion'])) ?>" class="btn btn-outline-primary btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
    <div class="text-muted small">
        Mostrando <?= e((string) count($asignaciones)) ?> de <?= e((string) $pagination['total']) ?> asignaciones.
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Paginacion de asignaciones docentes">
            <ul class="pagination mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/asignaciones-docentes?' . $queryForPage($page - 1))) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= e(url('/asignaciones-docentes?' . $queryForPage($i))) ?>"><?= e((string) $i) ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/asignaciones-docentes?' . $queryForPage($page + 1))) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
