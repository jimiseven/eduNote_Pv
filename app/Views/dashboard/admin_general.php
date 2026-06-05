<div class="row g-4">
    <div class="col-12">
        <div class="panel-card hero-card">
            <h2>Administracion general</h2>
            <p>Desde este panel se gestionaran los colegios, administradores y configuracion global del sistema.</p>
        </div>
    </div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/colegios')) ?>"><span>Colegios activos</span><strong><?= e((string) ($metrics['colegios_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/colegios')) ?>"><span>Colegios inactivos</span><strong><?= e((string) ($metrics['colegios_inactivos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/personal')) ?>"><span>Usuarios activos</span><strong><?= e((string) ($metrics['usuarios_activos'] ?? 0)) ?></strong></a></div>
    <div class="col-md-3"><a class="metric-card" href="<?= e(url('/personal')) ?>"><span>Admins colegio</span><strong><?= e((string) ($metrics['administradores_colegio'] ?? 0)) ?></strong></a></div>
</div>
