<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="h3 fw-bold mb-1">Docentes por materia</h2><p class="text-muted mb-0">Asignaciones docentes registradas.</p></div><a href="<?= e(url('/reportes')) ?>" class="btn btn-outline-secondary">Volver</a></div>
<?php require BASE_PATH . '/app/Views/reports/_gestion_filter.php'; ?>
<div class="panel-card p-0 overflow-hidden"><div class="table-responsive"><table class="table mb-0 align-middle"><thead class="table-light"><tr><th>Gestion</th><th>Docente</th><th>Curso</th><th>Materia</th><th>Estado</th></tr></thead><tbody>
<?php if(empty($rows)): ?><tr><td colspan="5" class="text-center text-muted py-4">Sin datos.</td></tr><?php endif; ?>
<?php foreach($rows as $row): ?><tr><td><?= e($row['gestion']) ?></td><td><strong><?= e($row['apellidos'].' '.$row['nombres']) ?></strong></td><td><?= e($row['nombre_nivel'].' '.$row['grado'].' '.$row['paralelo']) ?></td><td><?= e($row['nombre_materia']) ?></td><td><span class="badge <?= $row['estado']==='activo'?'text-bg-success':'text-bg-secondary' ?>"><?= e($row['estado']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></div>
