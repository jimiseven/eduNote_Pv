<?php $values = array_merge($matricula ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_matricula'])): ?><input type="hidden" name="id" value="<?= e((string)$values['id_matricula']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Estudiante *</label><select name="id_estudiante" class="form-select" required><option value="">Seleccionar</option><?php foreach($estudiantes as $e): ?><option value="<?= e((string)$e['id_estudiante']) ?>" <?= (int)($values['id_estudiante'] ?? 0)===(int)$e['id_estudiante']?'selected':'' ?>><?= e($e['apellido_paterno'].' '.$e['apellido_materno'].' '.$e['nombres'].' - '.$e['rude']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Gestion *</label><select name="id_gestion" class="form-select" required><option value="">Seleccionar</option><?php foreach($gestiones as $g): ?><option value="<?= e((string)$g['id_gestion']) ?>" <?= (int)($values['id_gestion'] ?? 0)===(int)$g['id_gestion']?'selected':'' ?>><?= e($g['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Curso *</label><select name="id_curso" class="form-select" required><option value="">Seleccionar</option><?php foreach($cursos as $c): ?><option value="<?= e((string)$c['id_curso']) ?>" <?= (int)($values['id_curso'] ?? 0)===(int)$c['id_curso']?'selected':'' ?>><?= e($c['nombre_nivel'].' '.$c['grado'].' '.$c['paralelo']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label">Fecha matricula</label><input type="date" name="fecha_matricula" class="form-control" value="<?= e($values['fecha_matricula'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-md-4"><label class="form-label">Estado</label><select name="estado" class="form-select"><?php foreach(['activo','retirado','promovido','reprobado','trasladado'] as $estado): ?><option value="<?= e($estado) ?>" <?= ($values['estado'] ?? 'activo')===$estado?'selected':'' ?>><?= e(ucfirst($estado)) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-8"><label class="form-label">Observacion</label><input name="observacion" class="form-control" value="<?= e($values['observacion'] ?? '') ?>"></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/matriculas')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
