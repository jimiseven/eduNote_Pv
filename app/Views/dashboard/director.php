<div class="row g-4">
    <div class="col-12">
        <div class="panel-card hero-card">
            <h2>Panel del director</h2>
            <p>Gestion actual: <strong><?= e($metrics['gestion_actual'] ?? 'Sin gestion configurada') ?></strong>. Consulta informacion academica, docentes, estudiantes y reportes del colegio.</p>
        </div>
    </div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/reportes/estudiantes-por-curso')) ?>"><span>Estudiantes activos</span><strong><?= e((string) ($metrics['estudiantes_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/reportes/docentes-por-materia')) ?>"><span>Docentes asignados</span><strong><?= e((string) ($metrics['docentes_asignados'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/reportes/estudiantes-por-curso')) ?>"><span>Cursos con matriculas</span><strong><?= e((string) ($metrics['cursos_con_matriculas'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card warning" href="<?= e(url('/reportes/notas-por-evaluacion')) ?>"><span>Evaluaciones abiertas</span><strong><?= e((string) ($metrics['evaluaciones_abiertas'] ?? 0)) ?></strong></a></div>
</div>
