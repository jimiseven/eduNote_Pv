<div class="mb-4">
    <h2 class="h3 fw-bold mb-1">Editar colegio</h2>
    <p class="text-muted mb-0">Actualiza la informacion institucional del colegio.</p>
</div>

<?php
$action = url('/colegios/actualizar');
$submitLabel = 'Guardar cambios';
require BASE_PATH . '/app/Views/colegios/_form.php';
?>
