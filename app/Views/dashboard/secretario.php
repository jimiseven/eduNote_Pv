<div class="row g-4">
    <div class="col-12">
        <div class="panel-card hero-card">
            <h2>Panel de secretaria</h2>
            <p>Gestion actual: <strong><?= e($metrics['gestion_actual'] ?? 'Sin gestion configurada') ?></strong>. Registra estudiantes, responsables, datos complementarios y matriculas.</p>
        </div>
    </div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/estudiantes')) ?>"><span>Estudiantes activos</span><strong><?= e((string) ($metrics['estudiantes_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card warning" href="<?= e(url('/matriculas')) ?>"><span>Sin matricula actual</span><strong><?= e((string) ($metrics['estudiantes_sin_matricula'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/responsables')) ?>"><span>Responsables</span><strong><?= e((string) ($metrics['responsables'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/matriculas')) ?>"><span>Matriculas activas</span><strong><?= e((string) ($metrics['matriculas_activas'] ?? 0)) ?></strong></a></div>
</div>
