<div class="mb-4">
    <h2 class="h3 fw-bold mb-1">Nuevo usuario</h2>
    <p class="text-muted mb-0">Crea administradores, directores, secretarios o profesores segun tu rol.</p>
</div>

<?php
$action = url('/personal');
$submitLabel = 'Crear usuario';
require BASE_PATH . '/app/Views/personal/_form.php';
?>
