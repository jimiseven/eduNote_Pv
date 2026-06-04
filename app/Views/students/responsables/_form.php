<?php $values = array_merge($responsable ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_responsable'])): ?><input type="hidden" name="id" value="<?= e((string)$values['id_responsable']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Nombres *</label><input name="nombres" class="form-control" value="<?= e($values['nombres'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Apellido paterno</label><input name="apellido_paterno" class="form-control" value="<?= e($values['apellido_paterno'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Apellido materno</label><input name="apellido_materno" class="form-control" value="<?= e($values['apellido_materno'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">CI</label><input name="carnet_identidad" class="form-control" value="<?= e($values['carnet_identidad'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Fecha nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control" value="<?= e($values['fecha_nacimiento'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Grado instruccion</label><select name="grado_instruccion" class="form-select"><option value="">Seleccionar</option><?php foreach(['Ninguno','Primaria','Secundaria','Tecnico','Universitario','Postgrado'] as $g): ?><option value="<?= e($g) ?>" <?= ($values['grado_instruccion'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Celular</label><input name="celular" class="form-control" value="<?= e($values['celular'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Idioma frecuente</label><input name="idioma_frecuente" class="form-control" value="<?= e($values['idioma_frecuente'] ?? '') ?>"></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/responsables')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
