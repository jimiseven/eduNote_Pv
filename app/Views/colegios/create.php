<div class="mb-4">
    <h2 class="h3 fw-bold mb-1">Nuevo colegio</h2>
    <p class="text-muted mb-0">Registra un colegio que usara la plataforma EduNote.</p>
</div>

<?php
$action = url('/colegios');
$submitLabel = 'Crear colegio';
require BASE_PATH . '/app/Views/colegios/_form.php';
?>
