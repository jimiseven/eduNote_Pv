<div class="row g-4">
    <div class="col-12">
        <div class="panel-card hero-card">
            <h2>Administracion del colegio</h2>
            <p>Gestion actual: <strong><?= e($metrics['gestion_actual'] ?? 'Sin gestion configurada') ?></strong>. Gestiona personal, cursos, materias, periodos academicos y asignaciones docentes.</p>
        </div>
    </div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/estudiantes')) ?>"><span>Estudiantes activos</span><strong><?= e((string) ($metrics['estudiantes_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/personal')) ?>"><span>Docentes activos</span><strong><?= e((string) ($metrics['docentes_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/cursos')) ?>"><span>Cursos activos</span><strong><?= e((string) ($metrics['cursos_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/materias')) ?>"><span>Materias activas</span><strong><?= e((string) ($metrics['materias_activas'] ?? 0)) ?></strong></a></div>
    <div class="col-md-4"><a class="metric-card" href="<?= e(url('/matriculas')) ?>"><span>Matriculas gestion actual</span><strong><?= e((string) ($metrics['matriculas_gestion'] ?? 0)) ?></strong></a></div>
    <div class="col-md-4"><a class="metric-card" href="<?= e(url('/reportes/notas-por-evaluacion')) ?>"><span>Evaluaciones registradas</span><strong><?= e((string) ($metrics['evaluaciones'] ?? 0)) ?></strong></a></div>
    <div class="col-md-4"><a class="metric-card warning" href="<?= e(url('/asignaciones-docentes')) ?>"><span>Docentes sin asignacion</span><strong><?= e((string) ($metrics['docentes_sin_asignacion'] ?? 0)) ?></strong></a></div>
</div>
