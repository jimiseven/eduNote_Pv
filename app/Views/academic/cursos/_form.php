<?php $values = array_merge($curso ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_curso'])): ?><input type="hidden" name="id" value="<?= e((string) $values['id_curso']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Nivel *</label><select name="id_nivel" class="form-select" required><option value="">Seleccionar</option><?php foreach ($niveles as $nivel): ?><option value="<?= e((string) $nivel['id_nivel']) ?>" <?= (int) ($values['id_nivel'] ?? 0) === (int) $nivel['id_nivel'] ? 'selected' : '' ?>><?= e($nivel['nombre_nivel']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Grado *</label><input type="number" name="grado" class="form-control" value="<?= e((string) ($values['grado'] ?? '')) ?>" required></div>
        <div class="col-md-3"><label class="form-label">Paralelo *</label><input type="text" name="paralelo" class="form-control" value="<?= e($values['paralelo'] ?? '') ?>" maxlength="5" required></div>
        <div class="col-md-2"><label class="form-label">Turno</label><input type="text" class="form-control" value="Manana" disabled></div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="estado" value="1" id="estado" <?= (int) ($values['estado'] ?? 1) === 1 ? 'checked' : '' ?>><label for="estado" class="form-check-label">Curso activo</label></div></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/cursos')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
