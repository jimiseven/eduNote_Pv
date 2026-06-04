<?php $values = array_merge($estudiante ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_estudiante'])): ?><input type="hidden" name="id" value="<?= e((string)$values['id_estudiante']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Nombres *</label><input name="nombres" class="form-control" value="<?= e($values['nombres'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Apellido paterno</label><input name="apellido_paterno" class="form-control" value="<?= e($values['apellido_paterno'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Apellido materno</label><input name="apellido_materno" class="form-control" value="<?= e($values['apellido_materno'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Genero</label><select name="genero" class="form-select"><option value="">Seleccionar</option><?php foreach(['Masculino','Femenino'] as $g): ?><option value="<?= e($g) ?>" <?= ($values['genero'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">RUDE *</label><input name="rude" class="form-control" value="<?= e($values['rude'] ?? '') ?>" required></div>
        <div class="col-md-3"><label class="form-label">CI</label><input name="carnet_identidad" class="form-control" value="<?= e($values['carnet_identidad'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Fecha nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control" value="<?= e($values['fecha_nacimiento'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Pais</label><input name="pais" class="form-control" value="<?= e($values['pais'] ?? 'Bolivia') ?>"></div>
        <div class="col-md-4"><label class="form-label">Provincia/Departamento</label><input name="provincia_departamento" class="form-control" value="<?= e($values['provincia_departamento'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Estado</label><select name="estado" class="form-select"><?php foreach(['activo','retirado','egresado','trasladado','inactivo'] as $estado): ?><option value="<?= e($estado) ?>" <?= ($values['estado'] ?? 'activo') === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/estudiantes')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
