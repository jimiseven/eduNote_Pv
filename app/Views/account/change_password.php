<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="panel-card">
            <h2 class="h3 fw-bold mb-2">Cambiar contrasena</h2>
            <p class="text-muted">Por seguridad debes cambiar tu contrasena temporal antes de continuar.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('/cuenta/cambiar-contrasena')) ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="password">Nueva contrasena</label>
                    <input type="password" class="form-control" id="password" name="password" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Confirmar contrasena</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar contrasena</button>
            </form>
        </div>
    </div>
</div>
