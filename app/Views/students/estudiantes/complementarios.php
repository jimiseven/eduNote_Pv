<?php
$nombreCompleto = trim(($estudiante['apellido_paterno'] ?? '') . ' ' . ($estudiante['apellido_materno'] ?? '') . ' ' . $estudiante['nombres']);
$value = static fn (string $section, string $field, string $default = ''): string => (string) (($data[$section][$field] ?? $default) ?? '');
$checked = static fn (string $section, string $field): string => !empty($data[$section][$field]) ? 'checked' : '';
$selected = static fn (string $section, string $field, string $option): string => (($data[$section][$field] ?? '') === $option) ? 'selected' : '';
$monthsSelected = array_filter(explode(',', (string) ($data['actividad_laboral']['meses_trabajo'] ?? '')));
$months = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$levels = ['ninguna' => 'Ninguna', 'leve' => 'Leve', 'grave' => 'Grave', 'muy_grave' => 'Muy grave', 'multiple' => 'Multiple'];
?>

<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1">Datos complementarios</h2>
        <p class="text-muted mb-0"><?= e($nombreCompleto) ?> - RUDE: <code><?= e($estudiante['rude']) ?></code></p>
    </div>
    <a href="<?= e(url('/estudiantes/ver?id=' . $estudiante['id_estudiante'])) ?>" class="btn btn-outline-secondary">Volver a ficha</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<form method="post" action="<?= e(url('/estudiantes/complementarios')) ?>" class="d-grid gap-4">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e((string) $estudiante['id_estudiante']) ?>">

    <section class="panel-card">
        <h3 class="h5 fw-bold mb-3">Direccion y contacto</h3>
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Departamento</label><input class="form-control" name="direccion[departamento]" value="<?= e($value('direccion', 'departamento')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Provincia</label><input class="form-control" name="direccion[provincia]" value="<?= e($value('direccion', 'provincia')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Municipio</label><input class="form-control" name="direccion[municipio]" value="<?= e($value('direccion', 'municipio')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Localidad</label><input class="form-control" name="direccion[localidad]" value="<?= e($value('direccion', 'localidad')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Comunidad</label><input class="form-control" name="direccion[comunidad]" value="<?= e($value('direccion', 'comunidad')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Zona</label><input class="form-control" name="direccion[zona]" value="<?= e($value('direccion', 'zona')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Nro vivienda</label><input class="form-control" name="direccion[numero_vivienda]" value="<?= e($value('direccion', 'numero_vivienda')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Telefono</label><input class="form-control" name="direccion[telefono]" value="<?= e($value('direccion', 'telefono')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Celular</label><input class="form-control" name="direccion[celular]" value="<?= e($value('direccion', 'celular')) ?>"></div>
        </div>
    </section>

    <section class="panel-card">
        <h3 class="h5 fw-bold mb-3">Salud y servicios basicos</h3>
        <div class="row g-3">
            <?php foreach (['tiene_seguro' => 'Tiene seguro', 'acceso_posta' => 'Acceso a posta', 'acceso_centro_salud' => 'Acceso centro salud', 'acceso_hospital' => 'Acceso hospital'] as $field => $label): ?>
                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="salud[<?= e($field) ?>]" id="salud_<?= e($field) ?>" <?= $checked('salud', $field) ?>><label class="form-check-label" for="salud_<?= e($field) ?>"><?= e($label) ?></label></div></div>
            <?php endforeach; ?>
            <?php foreach (['agua_caneria' => 'Agua por caneria', 'bano' => 'Bano', 'alcantarillado' => 'Alcantarillado', 'internet' => 'Internet', 'energia' => 'Energia electrica', 'recojo_basura' => 'Recojo basura'] as $field => $label): ?>
                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="servicios[<?= e($field) ?>]" id="servicios_<?= e($field) ?>" <?= $checked('servicios', $field) ?>><label class="form-check-label" for="servicios_<?= e($field) ?>"><?= e($label) ?></label></div></div>
            <?php endforeach; ?>
            <div class="col-md-3"><label class="form-label">Tipo vivienda</label><select class="form-select" name="servicios[tipo_vivienda]"><option value="">Seleccionar</option><?php foreach (['alquilada' => 'Alquilada', 'propia' => 'Propia', 'cedida' => 'Cedida', 'anticretico' => 'Anticretico'] as $option => $label): ?><option value="<?= e($option) ?>" <?= $selected('servicios', 'tipo_vivienda', $option) ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
        </div>
    </section>

    <section class="panel-card">
        <h3 class="h5 fw-bold mb-3">Transporte y dificultades</h3>
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Medio de transporte</label><select class="form-select" name="transporte[medio]"><option value="">Seleccionar</option><?php foreach (['a_pie' => 'A pie', 'vehiculo' => 'Vehiculo', 'fluvial' => 'Fluvial', 'otro' => 'Otro'] as $option => $label): ?><option value="<?= e($option) ?>" <?= $selected('transporte', 'medio', $option) ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label">Tiempo llegada</label><select class="form-select" name="transporte[tiempo_llegada]"><option value="">Seleccionar</option><option value="menos_media_hora" <?= $selected('transporte', 'tiempo_llegada', 'menos_media_hora') ?>>Menos de media hora</option><option value="mas_media_hora" <?= $selected('transporte', 'tiempo_llegada', 'mas_media_hora') ?>>Mas de media hora</option></select></div>
            <div class="col-md-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="dificultades[tiene_dificultad]" id="tiene_dificultad" <?= $checked('dificultades', 'tiene_dificultad') ?>><label class="form-check-label" for="tiene_dificultad">Tiene dificultad</label></div></div>
            <?php foreach (['auditiva' => 'Auditiva', 'visual' => 'Visual', 'intelectual' => 'Intelectual', 'fisico_motora' => 'Fisico motora', 'psiquica_mental' => 'Psiquica mental', 'autista' => 'Autista'] as $field => $label): ?>
                <div class="col-md-3"><label class="form-label"><?= e($label) ?></label><select class="form-select" name="dificultades[<?= e($field) ?>]"><?php foreach ($levels as $option => $optionLabel): ?><option value="<?= e($option) ?>" <?= $selected('dificultades', $field, $option) ?>><?= e($optionLabel) ?></option><?php endforeach; ?></select></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel-card">
        <h3 class="h5 fw-bold mb-3">Actividad laboral</h3>
        <div class="row g-3">
            <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="actividad_laboral[trabajo]" id="trabajo" <?= $checked('actividad_laboral', 'trabajo') ?>><label class="form-check-label" for="trabajo">Trabaja</label></div></div>
            <div class="col-md-5"><label class="form-label">Actividad</label><input class="form-control" name="actividad_laboral[actividad]" value="<?= e($value('actividad_laboral', 'actividad')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Frecuencia</label><select class="form-select" name="actividad_laboral[frecuencia]"><option value="">Seleccionar</option><?php foreach (['todos_dias' => 'Todos los dias', 'dias_habiles' => 'Dias habiles', 'fin_de_semana' => 'Fin de semana', 'esporadico' => 'Esporadico', 'dias_festivos' => 'Dias festivos', 'vacaciones' => 'Vacaciones'] as $option => $label): ?><option value="<?= e($option) ?>" <?= $selected('actividad_laboral', 'frecuencia', $option) ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
            <?php foreach (['turno_manana' => 'Turno manana', 'turno_tarde' => 'Turno tarde', 'turno_noche' => 'Turno noche'] as $field => $label): ?>
                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="actividad_laboral[<?= e($field) ?>]" id="<?= e($field) ?>" <?= $checked('actividad_laboral', $field) ?>><label class="form-check-label" for="<?= e($field) ?>"><?= e($label) ?></label></div></div>
            <?php endforeach; ?>
            <div class="col-12"><label class="form-label">Meses de trabajo</label><div class="row g-2"><?php foreach ($months as $month): ?><div class="col-6 col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="actividad_laboral[meses_trabajo][]" id="mes_<?= e($month) ?>" value="<?= e($month) ?>" <?= in_array($month, $monthsSelected, true) ? 'checked' : '' ?>><label class="form-check-label" for="mes_<?= e($month) ?>"><?= e(ucfirst($month)) ?></label></div></div><?php endforeach; ?></div></div>
        </div>
    </section>

    <section class="panel-card">
        <h3 class="h5 fw-bold mb-3">Idioma, cultura y abandono</h3>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Idioma frecuente</label><input class="form-control" name="idioma_cultura[idioma]" value="<?= e($value('idioma_cultura', 'idioma')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Cultura/Nacion</label><input class="form-control" name="idioma_cultura[cultura]" value="<?= e($value('idioma_cultura', 'cultura')) ?>"></div>
            <div class="col-md-2 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="abandono[abandono]" id="abandono" <?= $checked('abandono', 'abandono') ?>><label class="form-check-label" for="abandono">Abandono</label></div></div>
            <div class="col-md-2"><label class="form-label">Motivo</label><select class="form-select" name="abandono[motivo]"><option value="">Seleccionar</option><?php foreach (['trabajo' => 'Trabajo', 'falta_dinero' => 'Falta dinero', 'otro' => 'Otro'] as $option => $label): ?><option value="<?= e($option) ?>" <?= $selected('abandono', 'motivo', $option) ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
        </div>
    </section>

    <div class="d-flex justify-content-end gap-2">
        <a href="<?= e(url('/estudiantes/ver?id=' . $estudiante['id_estudiante'])) ?>" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar datos complementarios</button>
    </div>
</form>
