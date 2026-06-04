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
                        <td colspan="7" class="text-center text-muted py-4">No hay usuarios registrados.</td>
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
