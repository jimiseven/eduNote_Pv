<div class="mb-4">
    <h2 class="h3 fw-bold mb-1">Editar usuario</h2>
    <p class="text-muted mb-0">Actualiza datos de acceso, rol y estado del usuario.</p>
</div>

<?php
$action = url('/personal/actualizar');
$submitLabel = 'Guardar cambios';
require BASE_PATH . '/app/Views/personal/_form.php';
?>
