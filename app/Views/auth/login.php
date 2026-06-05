<main class="auth-page">
    <section class="auth-card shadow-lg">
        <div class="auth-intro">
            <span class="brand-mark large">EN</span>
            <h1>EduNote</h1>
            <p>Administracion academica multi-colegio para estudiantes, docentes, materias y notas.</p>
        </div>

        <form method="post" action="<?= e(url('/login')) ?>" class="auth-form">
            <?= csrf_field() ?>
            <h2>Iniciar sesion</h2>
            <p class="text-muted">Ingresa con tu usuario y contrasena.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2"><?= e($error) ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Contrasena</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>

            <div class="demo-users">
                <strong>Usuarios iniciales</strong>
                <span>superadmin / admin123</span>
                <span>admin_demo / admin123</span>
            </div>
        </form>
    </section>
</main>
