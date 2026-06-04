<?php $values = array_merge($periodo ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_periodo'])): ?><input type="hidden" name="id" value="<?= e((string) $values['id_periodo']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Gestion *</label><select name="id_gestion" class="form-select" required><option value="">Seleccionar</option><?php foreach ($gestiones as $gestion): ?><option value="<?= e((string) $gestion['id_gestion']) ?>" <?= (int) ($values['id_gestion'] ?? 0) === (int) $gestion['id_gestion'] ? 'selected' : '' ?>><?= e($gestion['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Numero *</label><input type="number" name="numero_periodo" class="form-control" value="<?= e((string) ($values['numero_periodo'] ?? '')) ?>" required></div>
        <div class="col-md-5"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" value="<?= e($values['nombre'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" class="form-control" value="<?= e($values['fecha_inicio'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" class="form-control" value="<?= e($values['fecha_fin'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Estado</label><select name="estado" class="form-select"><?php foreach (['pendiente','activo','cerrado'] as $estado): ?><option value="<?= e($estado) ?>" <?= ($values['estado'] ?? 'pendiente') === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/periodos')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
