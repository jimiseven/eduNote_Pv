<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Colegios</h2>
        <p class="text-muted mb-0">Administra los colegios que usaran el sistema.</p>
    </div>
    <a href="<?= e(url('/colegios/crear')) ?>" class="btn btn-primary">Nuevo colegio</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Codigo</th>
                    <th>Contacto</th>
                    <th>Ubicacion</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($colegios)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay colegios registrados.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($colegios as $colegio): ?>
                    <tr>
                        <td>
                            <strong><?= e($colegio['nombre']) ?></strong>
                            <?php if (!empty($colegio['nit'])): ?>
                                <div class="small text-muted">NIT: <?= e($colegio['nit']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><code><?= e($colegio['codigo']) ?></code></td>
                        <td>
                            <div><?= e($colegio['telefono'] ?? '-') ?></div>
                            <div class="small text-muted"><?= e($colegio['correo'] ?? '') ?></div>
                        </td>
                        <td>
                            <div><?= e($colegio['ciudad'] ?? '-') ?></div>
                            <div class="small text-muted"><?= e($colegio['departamento'] ?? '') ?></div>
                        </td>
                        <td>
                            <?php if ((int) $colegio['estado'] === 1): ?>
                                <span class="badge text-bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= e(url('/colegios/editar?id=' . $colegio['id_colegio'])) ?>" class="btn btn-outline-primary btn-sm">Editar</a>
                                <form method="post" action="<?= e(url('/colegios/cambiar-estado')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $colegio['id_colegio']) ?>">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                        <?= (int) $colegio['estado'] === 1 ? 'Inactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
