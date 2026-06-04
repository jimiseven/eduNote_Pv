<?php $values = $configuracion ?? []; ?>
<div class="mb-4"><h2 class="h3 fw-bold mb-1">Configuracion academica</h2><p class="text-muted mb-0">Define gestion actual, periodos y escala de notas del colegio.</p></div>
<?php if (!empty($success)): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e(url('/configuracion-academica')) ?>" class="panel-card">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Gestion actual</label><select name="id_gestion_actual" class="form-select"><option value="">Sin seleccionar</option><?php foreach ($gestiones as $gestion): ?><option value="<?= e((string) $gestion['id_gestion']) ?>" <?= (int) ($values['id_gestion_actual'] ?? 0) === (int) $gestion['id_gestion'] ? 'selected' : '' ?>><?= e($gestion['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label">Cantidad de periodos</label><input type="number" name="cantidad_periodos" class="form-control" value="<?= e((string) ($values['cantidad_periodos'] ?? 3)) ?>" min="1"></div>
        <div class="col-md-4"><label class="form-label">Nota minima</label><input type="number" step="0.01" name="escala_nota_minima" class="form-control" value="<?= e((string) ($values['escala_nota_minima'] ?? '0.00')) ?>"></div>
        <div class="col-md-4"><label class="form-label">Nota maxima</label><input type="number" step="0.01" name="escala_nota_maxima" class="form-control" value="<?= e((string) ($values['escala_nota_maxima'] ?? '100.00')) ?>"></div>
        <div class="col-md-4"><label class="form-label">Nota aprobacion</label><input type="number" step="0.01" name="nota_aprobacion" class="form-control" value="<?= e((string) ($values['nota_aprobacion'] ?? '51.00')) ?>"></div>
    </div>
    <div class="d-flex justify-content-end mt-4"><button class="btn btn-primary">Guardar configuracion</button></div>
</form>
