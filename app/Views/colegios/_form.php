<?php
$values = array_merge($colegio ?? [], $old ?? []);
$action = $action ?? url('/colegios');
$submitLabel = $submitLabel ?? 'Guardar';
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Revisa los datos:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="panel-card">
    <?= csrf_field() ?>
    <?php if (!empty($values['id_colegio'])): ?>
        <input type="hidden" name="id" value="<?= e((string) $values['id_colegio']) ?>">
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="nombre">Nombre del colegio *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= e($values['nombre'] ?? '') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="codigo">Codigo *</label>
            <input type="text" class="form-control" id="codigo" name="codigo" value="<?= e($values['codigo'] ?? '') ?>" required>
            <div class="form-text">Ejemplo: COLEGIO_SAN_JOSE</div>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="nit">NIT</label>
            <input type="text" class="form-control" id="nit" name="nit" value="<?= e($values['nit'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="telefono">Telefono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= e($values['telefono'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="correo">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?= e($values['correo'] ?? '') ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label" for="direccion">Direccion</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= e($values['direccion'] ?? '') ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="ciudad">Ciudad</label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?= e($values['ciudad'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="departamento">Departamento</label>
            <input type="text" class="form-control" id="departamento" name="departamento" value="<?= e($values['departamento'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="pais">Pais</label>
            <input type="text" class="form-control" id="pais" name="pais" value="<?= e($values['pais'] ?? 'Bolivia') ?>">
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="estado" name="estado" value="1" <?= (int) ($values['estado'] ?? 1) === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="estado">Colegio activo</label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="<?= e(url('/colegios')) ?>" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
    </div>
</form>
