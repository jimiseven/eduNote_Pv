<?php $values = array_merge($item ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_curso_materia'])): ?><input type="hidden" name="id" value="<?= e((string)$values['id_curso_materia']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Curso *</label><select name="id_curso" class="form-select" required><option value="">Seleccionar</option><?php foreach ($cursos as $curso): ?><option value="<?= e((string)$curso['id_curso']) ?>" <?= (int)($values['id_curso'] ?? 0) === (int)$curso['id_curso'] ? 'selected' : '' ?>><?= e($curso['nombre_nivel'] . ' ' . $curso['grado'] . ' ' . $curso['paralelo']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label">Materia *</label><select name="id_materia" class="form-select" required><option value="">Seleccionar</option><?php foreach ($materias as $materia): ?><option value="<?= e((string)$materia['id_materia']) ?>" <?= (int)($values['id_materia'] ?? 0) === (int)$materia['id_materia'] ? 'selected' : '' ?>><?= e($materia['nombre_materia']) ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="estado" id="estado" value="1" <?= (int)($values['estado'] ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="estado">Asignacion activa</label></div></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/cursos-materias')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
