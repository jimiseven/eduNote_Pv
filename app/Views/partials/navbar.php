<?php $authUser = App\Core\Auth::user(); ?>

<header class="app-navbar">
    <div>
        <h1 class="h4 mb-0"><?= e($title ?? 'Panel') ?></h1>
        <span class="text-muted small">
            <?= e($authUser['nombre_rol'] ?? '') ?>
            <?php if (!empty($authUser['colegio'])): ?>
                | <?= e($authUser['colegio']) ?>
            <?php endif; ?>
        </span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="small text-muted"><?= e(($authUser['nombres'] ?? '') . ' ' . ($authUser['apellidos'] ?? '')) ?></span>
        <a href="<?= e(url('/cuenta/cambiar-contrasena')) ?>" class="btn btn-outline-primary btn-sm">Cambiar contrasena</a>
        <a href="<?= e(url('/logout')) ?>" class="btn btn-outline-danger btn-sm">Salir</a>
    </div>
</header>
