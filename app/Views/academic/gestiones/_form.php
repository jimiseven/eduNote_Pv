<?php $values = array_merge($gestion ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_gestion'])): ?><input type="hidden" name="id" value="<?= e((string) $values['id_gestion']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-3"><label class="form-label">Anio *</label><input type="number" name="anio" class="form-control" value="<?= e((string) ($values['anio'] ?? date('Y'))) ?>" required></div>
        <div class="col-md-5"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" value="<?= e($values['nombre'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Estado</label><select name="estado" class="form-select"><?php foreach (['planificada','activa','cerrada'] as $estado): ?><option value="<?= e($estado) ?>" <?= ($values['estado'] ?? 'planificada') === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" class="form-control" value="<?= e($values['fecha_inicio'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" class="form-control" value="<?= e($values['fecha_fin'] ?? '') ?>"></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/gestiones')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
