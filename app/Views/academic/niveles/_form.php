<?php $values = array_merge($nivel ?? [], $old ?? []); ?>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_nivel'])): ?><input type="hidden" name="id" value="<?= e((string) $values['id_nivel']) ?>"><?php endif; ?>
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Nombre del nivel *</label><input type="text" name="nombre_nivel" class="form-control" value="<?= e($values['nombre_nivel'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Orden</label><input type="number" name="orden" class="form-control" value="<?= e((string) ($values['orden'] ?? '')) ?>"></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="<?= e(url('/niveles')) ?>" class="btn btn-outline-secondary">Cancelar</a><button class="btn btn-primary"><?= e($submitLabel) ?></button></div>
</form>
