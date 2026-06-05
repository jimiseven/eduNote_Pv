<?php
$filters = $filters ?? ['q' => '', 'id_rol' => 0, 'estado' => ''];
$pagination = $pagination ?? ['total' => count($personal ?? []), 'page' => 1, 'per_page' => 10, 'total_pages' => 1];
$page = (int) $pagination['page'];
$totalPages = (int) $pagination['total_pages'];
$queryForPage = function (int $targetPage) use ($filters): string {
    return http_build_query(array_filter([
        'q' => $filters['q'] ?? '',
        'id_rol' => (int) ($filters['id_rol'] ?? 0) ?: '',
        'estado' => $filters['estado'] ?? '',
        'page' => $targetPage,
    ], static fn ($value) => $value !== '' && $value !== null));
};
?>

<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1"><?= e($title) ?></h2>
        <p class="text-muted mb-0">Gestiona usuarios, roles y accesos al sistema.</p>
    </div>
    <a href="<?= e(url('/personal/crear')) ?>" class="btn btn-primary">Nuevo usuario</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel-card p-3 mb-3">
    <form method="get" action="<?= e(url('/personal')) ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="q">Buscar</label>
            <input type="search" class="form-control" id="q" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Nombre, usuario, CI o celular">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="id_rol">Rol</label>
            <select class="form-select" id="id_rol" name="id_rol">
                <option value="">Todos</option>
                <?php foreach (($roles ?? []) as $rol): ?>
                    <option value="<?= e((string) $rol['id_rol']) ?>" <?= (int) ($filters['id_rol'] ?? 0) === (int) $rol['id_rol'] ? 'selected' : '' ?>><?= e($rol['nombre_rol']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="">Todos</option>
                <option value="1" <?= ($filters['estado'] ?? '') === '1' ? 'selected' : '' ?>>Habilitado</option>
                <option value="0" <?= ($filters['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inhabilitado</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
            <a href="<?= e(url('/personal')) ?>" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

<div class="panel-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Colegio</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($personal)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay usuarios para los filtros seleccionados.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($personal as $persona): ?>
                    <tr>
                        <td>
                            <strong><?= e($persona['nombres'] . ' ' . $persona['apellidos']) ?></strong>
                            <?php if (!empty($persona['carnet_identidad'])): ?>
                                <div class="small text-muted">CI: <?= e($persona['carnet_identidad']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><code><?= e($persona['usuario']) ?></code></td>
                        <td><?= e($persona['nombre_rol']) ?></td>
                        <td><?= e($persona['colegio'] ?? 'Global') ?></td>
                        <td><?= e($persona['celular'] ?? '-') ?></td>
                        <td>
                            <?php if ((int) $persona['estado'] === 1): ?>
                                <span class="badge text-bg-success">Habilitado</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inhabilitado</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= e(url('/personal/editar?id=' . $persona['id_personal'])) ?>" class="btn btn-outline-primary btn-sm">Editar</a>
                                <?php if ((int) $persona['id_personal'] !== (int) $authUser['id_personal']): ?>
                                    <form method="post" action="<?= e(url('/personal/cambiar-estado')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $persona['id_personal']) ?>">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                            <?= (int) $persona['estado'] === 1 ? 'Inhabilitar' : 'Habilitar' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
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
        Mostrando <?= e((string) count($personal)) ?> de <?= e((string) $pagination['total']) ?> usuarios.
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Paginacion de personal">
            <ul class="pagination mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/personal?' . $queryForPage($page - 1))) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= e(url('/personal?' . $queryForPage($i))) ?>"><?= e((string) $i) ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= e(url('/personal?' . $queryForPage($page + 1))) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
