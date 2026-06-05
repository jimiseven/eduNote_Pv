<div class="row g-4">
    <div class="col-12">
        <div class="panel-card hero-card">
            <h2>Panel del profesor</h2>
            <p>Consulta tus materias asignadas, crea evaluaciones y carga notas de 0 a 100.</p>
        </div>
    </div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/profesor/materias')) ?>"><span>Materias asignadas</span><strong><?= e((string) ($metrics['materias_asignadas'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card warning" href="<?= e(url('/profesor/evaluaciones')) ?>"><span>Evaluaciones abiertas</span><strong><?= e((string) ($metrics['evaluaciones_abiertas'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/profesor/evaluaciones')) ?>"><span>Evaluaciones cerradas</span><strong><?= e((string) ($metrics['evaluaciones_cerradas'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/profesor/evaluaciones')) ?>"><span>Notas cargadas</span><strong><?= e((string) ($metrics['notas_cargadas'] ?? 0)) ?></strong></a></div>
</div>
