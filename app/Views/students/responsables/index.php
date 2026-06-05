<?php
$filters = $filters ?? ['q' => ''];
$pagination = $pagination ?? ['total' => count($responsables ?? []), 'page' => 1, 'per_page' => 10, 'total_pages' => 1];
$page = (int) $pagination['page'];
$totalPages = (int) $pagination['total_pages'];
$queryForPage = function (int $targetPage) use ($filters): string {
    return http_build_query(array_filter([
        'q' => $filters['q'] ?? '',
        'page' => $targetPage,
    ], static fn ($value) => $value !== '' && $value !== null));
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Responsables</h2>
        <p class="text-muted mb-0">Padres, madres, tutores y contactos.</p>
    </div>
    <a href="<?= e(url('/responsables/crear')) ?>" class="btn btn-primary">Nuevo responsable</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel-card p-3 mb-3">
    <form method="get" action="<?= e(url('/responsables')) ?>" class="row g-3 align-items-end">
        <div class="col-md-9">
            <label class="form-label" for="q">Buscar</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Nombre, CI o celular">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
            <a href="<?= e(url('/responsables')) ?>" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

<div class="panel-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Responsable</th>
                    <th>CI</th>
                    <th>Celular</th>
                    <th>Instruccion</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($responsables)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay responsables para los filtros seleccionados.</td></tr>
                <?php endif; ?>
                <?php foreach ($responsables as $responsable): ?>
                    <tr>
                        <td><strong><?= e(trim(($responsable['apellido_paterno'] ?? '') . ' ' . ($responsable['apellido_materno'] ?? '') . ' ' . $responsable['nombres'])) ?></strong></td>
                        <td><?= e($responsable['carnet_identidad'] ?? '-') ?></td>
                        <td><?= e($responsable['celular'] ?? '-') ?></td>
                        <td><?= e($responsable['grado_instruccion'] ?? '-') ?></td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= e(url('/responsables/editar?id=' . $responsable['id_responsable'])) ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
    <div class="text-muted small">
        Mostrando <?= e((string) count($responsables)) ?> de <?= e((string) $pagination['total']) ?> responsables.
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Paginacion de responsables">
            <ul class="pagination mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/responsables?' . $queryForPage($page - 1))) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= e(url('/responsables?' . $queryForPage($i))) ?>"><?= e((string) $i) ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/responsables?' . $queryForPage($page + 1))) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
