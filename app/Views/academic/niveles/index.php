<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="h3 fw-bold mb-1">Niveles</h2><p class="text-muted mb-0">Inicial, Primaria, Secundaria u otros niveles.</p></div><a href="<?= e(url('/niveles/crear')) ?>" class="btn btn-primary">Nuevo nivel</a></div>
<?php if (!empty($success)): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<div class="panel-card p-0 overflow-hidden"><table class="table mb-0 align-middle"><thead class="table-light"><tr><th>Orden</th><th>Nivel</th><th class="text-end">Acciones</th></tr></thead><tbody>
<?php if (empty($niveles)): ?><tr><td colspan="3" class="text-center text-muted py-4">No hay niveles registrados.</td></tr><?php endif; ?>
<?php foreach ($niveles as $nivel): ?><tr><td><?= e((string) ($nivel['orden'] ?? '-')) ?></td><td><strong><?= e($nivel['nombre_nivel']) ?></strong></td><td class="text-end"><a href="<?= e(url('/niveles/editar?id=' . $nivel['id_nivel'])) ?>" class="btn btn-outline-primary btn-sm">Editar</a></td></tr><?php endforeach; ?>
</tbody></table></div>
