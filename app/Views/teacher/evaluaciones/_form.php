<?php $values = array_merge($evaluacion ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_evaluacion'])): ?><input type="hidden" name="id" value="<?= e((string)$values['id_evaluacion']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Curso / Materia *</label><select name="id_asignacion" class="form-select" required><option value="">Seleccionar</option><?php foreach($asignaciones as $a): ?><option value="<?= e((string)$a['id_asignacion']) ?>" <?= (int)($values['id_asignacion'] ?? 0)===(int)$a['id_asignacion']?'selected':'' ?>><?= e($a['gestion'].' - '.$a['nombre_nivel'].' '.$a['grado'].' '.$a['paralelo'].' - '.$a['nombre_materia']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label">Periodo *</label><select name="id_periodo" class="form-select" required><option value="">Seleccionar</option><?php foreach($periodos as $p): ?><option value="<?= e((string)$p['id_periodo']) ?>" <?= (int)($values['id_periodo'] ?? 0)===(int)$p['id_periodo']?'selected':'' ?>><?= e($p['gestion_nombre'].' - '.$p['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-5"><label class="form-label">Nombre *</label><input name="nombre" class="form-control" value="<?= e($values['nombre'] ?? '') ?>" required></div>
        <div class="col-md-3"><label class="form-label">Tipo</label><select name="tipo" class="form-select"><?php foreach(['tarea','practica','examen','participacion','proyecto','otro'] as $tipo): ?><option value="<?= e($tipo) ?>" <?= ($values['tipo'] ?? 'otro')===$tipo?'selected':'' ?>><?= e(ucfirst($tipo)) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Ponderacion</label><input type="number" step="0.01" name="ponderacion" class="form-control" value="<?= e((string)($values['ponderacion'] ?? '100.00')) ?>"></div>
        <div class="col-md-2"><label class="form-label">Fecha</label><input type="date" name="fecha" class="form-control" value="<?= e($values['fecha'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-md-3"><label class="form-label">Estado</label><select name="estado" class="form-select"><option value="abierta" <?= ($values['estado'] ?? 'abierta')==='abierta'?'selected':'' ?>>Abierta</option><option value="cerrada" <?= ($values['estado'] ?? '')==='cerrada'?'selected':'' ?>>Cerrada</option></select></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/profesor/evaluaciones')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
