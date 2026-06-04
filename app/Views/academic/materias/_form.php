<?php $values = array_merge($materia ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_materia'])): ?><input type="hidden" name="id" value="<?= e((string) $values['id_materia']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Nombre de materia *</label><input type="text" name="nombre_materia" class="form-control" value="<?= e($values['nombre_materia'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Materia padre</label><select name="materia_padre_id" class="form-select"><option value="">Sin materia padre</option><?php foreach ($materiasPadre as $padre): ?><?php if ((int)($padre['id_materia'] ?? 0) !== (int)($values['id_materia'] ?? 0)): ?><option value="<?= e((string) $padre['id_materia']) ?>" <?= (int)($values['materia_padre_id'] ?? 0) === (int)$padre['id_materia'] ? 'selected' : '' ?>><?= e($padre['nombre_materia']) ?></option><?php endif; ?><?php endforeach; ?></select></div>
        <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="es_submateria" id="es_submateria" value="1" <?= (int)($values['es_submateria'] ?? 0) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="es_submateria">Es submateria</label></div></div>
        <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="es_extra" id="es_extra" value="1" <?= (int)($values['es_extra'] ?? 0) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="es_extra">Materia extra</label></div></div>
        <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="estado" id="estado" value="1" <?= (int)($values['estado'] ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="estado">Activa</label></div></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/materias')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
