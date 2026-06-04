<div class="mb-4"><h2 class="h3 fw-bold mb-1">Reportes</h2><p class="text-muted mb-0">Consultas basicas del colegio.</p></div>
<div class="row g-4">
    <div class="col-md-3"><a class="panel-card d-block text-decoration-none text-dark" href="<?= e(url('/reportes/estudiantes-por-curso')) ?>"><h3 class="h5 fw-bold">Estudiantes por curso</h3><p>Totales por gestion, nivel, grado y paralelo.</p></a></div>
    <div class="col-md-3"><a class="panel-card d-block text-decoration-none text-dark" href="<?= e(url('/reportes/docentes-por-materia')) ?>"><h3 class="h5 fw-bold">Docentes por materia</h3><p>Asignaciones docentes por curso y gestion.</p></a></div>
    <div class="col-md-3"><a class="panel-card d-block text-decoration-none text-dark" href="<?= e(url('/reportes/responsables-por-estudiante')) ?>"><h3 class="h5 fw-bold">Responsables</h3><p>Responsables asociados a estudiantes.</p></a></div>
    <div class="col-md-3"><a class="panel-card d-block text-decoration-none text-dark" href="<?= e(url('/reportes/notas-por-evaluacion')) ?>"><h3 class="h5 fw-bold">Notas</h3><p>Notas registradas por evaluacion.</p></a></div>
</div>
