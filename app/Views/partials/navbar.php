<?php $authUser = App\Core\Auth::user(); ?>

<header class="app-navbar">
    <div>
        <h1 class="h4 mb-0" id="app-page-title"><?= e($title ?? 'Panel') ?></h1>
        <span class="text-muted small">
            <?= e($authUser['nombre_rol'] ?? '') ?>
            <?php if (!empty($authUser['colegio'])): ?>
                | <?= e($authUser['colegio']) ?>
            <?php endif; ?>
        </span>
    </div>
</header>
