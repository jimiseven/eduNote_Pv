<form method="get" class="report-filter no-print">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Filtrar por gestion</label>
            <select name="gestion" class="form-select">
                <option value="">Todas las gestiones</option>
                <?php foreach ($gestiones ?? [] as $gestion): ?>
                    <option value="<?= e((string) $gestion['id_gestion']) ?>" <?= (int) ($selectedGestion ?? 0) === (int) $gestion['id_gestion'] ? 'selected' : '' ?>>
                        <?= e($gestion['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary">Aplicar</button>
            <a href="<?= e(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: url('/reportes')) ?>" class="btn btn-outline-secondary">Limpiar</a>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">Imprimir</button>
        </div>
    </div>
</form>
