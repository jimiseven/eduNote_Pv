<div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="h3 fw-bold mb-1">Estudiantes por curso</h2><p class="text-muted mb-0">Cantidad de matriculas por curso y gestion.</p></div><a href="<?= e(url('/reportes')) ?>" class="btn btn-outline-secondary">Volver</a></div>
<?php require BASE_PATH . '/app/Views/reports/_gestion_filter.php'; ?>
<div class="panel-card p-0 overflow-hidden"><div class="table-responsive"><table class="table mb-0 align-middle"><thead class="table-light"><tr><th>Gestion</th><th>Nivel</th><th>Grado</th><th>Paralelo</th><th>Total</th></tr></thead><tbody>
<?php if(empty($rows)): ?><tr><td colspan="5" class="text-center text-muted py-4">Sin datos.</td></tr><?php endif; ?>
<?php foreach($rows as $row): ?><tr><td><?= e($row['gestion']) ?></td><td><?= e($row['nombre_nivel']) ?></td><td><?= e((string)$row['grado']) ?></td><td><?= e($row['paralelo']) ?></td><td><strong><?= e((string)$row['total']) ?></strong></td></tr><?php endforeach; ?>
</tbody></table></div></div>
