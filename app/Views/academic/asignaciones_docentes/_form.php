<?php $values = array_merge($asignacion ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_asignacion'])): ?><input type="hidden" name="id" value="<?= e((string)$values['id_asignacion']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Gestion *</label><select name="id_gestion" class="form-select" required><option value="">Seleccionar</option><?php foreach ($gestiones as $gestion): ?><option value="<?= e((string)$gestion['id_gestion']) ?>" <?= (int)($values['id_gestion'] ?? 0) === (int)$gestion['id_gestion'] ? 'selected' : '' ?>><?= e($gestion['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label">Docente *</label><select name="id_personal" class="form-select" required><option value="">Seleccionar</option><?php foreach ($docentes as $docente): ?><option value="<?= e((string)$docente['id_personal']) ?>" <?= (int)($values['id_personal'] ?? 0) === (int)$docente['id_personal'] ? 'selected' : '' ?>><?= e($docente['apellidos'] . ' ' . $docente['nombres']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label">Curso / Materia *</label><select name="id_curso_materia" class="form-select" required><option value="">Seleccionar</option><?php foreach ($cursoMaterias as $cm): ?><option value="<?= e((string)$cm['id_curso_materia']) ?>" <?= (int)($values['id_curso_materia'] ?? 0) === (int)$cm['id_curso_materia'] ? 'selected' : '' ?>><?= e($cm['nombre_nivel'] . ' ' . $cm['grado'] . ' ' . $cm['paralelo'] . ' - ' . $cm['nombre_materia']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label">Estado</label><select name="estado" class="form-select"><option value="activo" <?= ($values['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= ($values['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/asignaciones-docentes')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
