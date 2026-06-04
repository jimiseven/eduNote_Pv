<?php
$values = array_merge($persona ?? [], $old ?? []);
$action = $action ?? url('/personal');
$submitLabel = $submitLabel ?? 'Guardar';
$isAdminGeneral = ($authUser['nombre_rol'] ?? '') === 'Administrador General';
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
    <?php if (!empty($values['id_personal'])): ?>
        <input type="hidden" name="id" value="<?= e((string) $values['id_personal']) ?>">
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="nombres">Nombres *</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= e($values['nombres'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="apellidos">Apellidos *</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= e($values['apellidos'] ?? '') ?>" required>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="id_rol">Rol *</label>
            <select class="form-select" id="id_rol" name="id_rol" required>
                <option value="">Seleccionar</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= e((string) $rol['id_rol']) ?>" <?= (int) ($values['id_rol'] ?? 0) === (int) $rol['id_rol'] ? 'selected' : '' ?>>
                        <?= e($rol['nombre_rol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="id_colegio">Colegio</label>
            <?php if ($isAdminGeneral): ?>
                <select class="form-select" id="id_colegio" name="id_colegio">
                    <option value="">Sin colegio</option>
                    <?php foreach ($colegios as $colegio): ?>
                        <option value="<?= e((string) $colegio['id_colegio']) ?>" <?= (int) ($values['id_colegio'] ?? 0) === (int) $colegio['id_colegio'] ? 'selected' : '' ?>>
                            <?= e($colegio['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Solo Administrador General puede quedar sin colegio.</div>
            <?php else: ?>
                <input type="hidden" name="id_colegio" value="<?= e((string) $authUser['id_colegio']) ?>">
                <input type="text" class="form-control" value="<?= e($authUser['colegio'] ?? 'Colegio actual') ?>" disabled>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="carnet_identidad">Carnet de identidad</label>
            <input type="text" class="form-control" id="carnet_identidad" name="carnet_identidad" value="<?= e($values['carnet_identidad'] ?? '') ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="celular">Celular</label>
            <input type="text" class="form-control" id="celular" name="celular" value="<?= e($values['celular'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="usuario">Usuario *</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= e($values['usuario'] ?? '') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="password">Contrasena <?= empty($values['id_personal']) ? '*' : '' ?></label>
            <input type="password" class="form-control" id="password" name="password" <?= empty($values['id_personal']) ? 'required' : '' ?>>
            <?php if (!empty($values['id_personal'])): ?>
                <div class="form-text">Dejar vacio para mantener la actual.</div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="estado" name="estado" value="1" <?= (int) ($values['estado'] ?? 1) === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="estado">Usuario habilitado</label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="<?= e(url('/personal')) ?>" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
    </div>
</form>
