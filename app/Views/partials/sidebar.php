<?php
$authUser = App\Core\Auth::user();

$groups = [
    'Administrador General' => [
        [
            'title' => 'Panel',
            'items' => [
                ['label' => 'Panel general', 'path' => '/admin-general/dashboard'],
            ],
        ],
        [
            'title' => 'Sistema',
            'items' => [
                ['label' => 'Colegios', 'path' => '/colegios'],
                ['label' => 'Administradores', 'path' => '/personal'],
            ],
        ],
    ],
    'Administrador Colegio' => [
        [
            'title' => 'Panel',
            'items' => [
                ['label' => 'Panel colegio', 'path' => '/admin-colegio/dashboard'],
            ],
        ],
        [
            'title' => 'Administracion',
            'items' => [
                ['label' => 'Personal', 'path' => '/personal'],
                ['label' => 'Configuracion', 'path' => '/configuracion-academica'],
            ],
        ],
        [
            'title' => 'Academico',
            'items' => [
                ['label' => 'Gestiones', 'path' => '/gestiones'],
                ['label' => 'Periodos', 'path' => '/periodos'],
                ['label' => 'Niveles', 'path' => '/niveles'],
                ['label' => 'Cursos', 'path' => '/cursos'],
                ['label' => 'Materias', 'path' => '/materias'],
                ['label' => 'Materias por curso', 'path' => '/cursos-materias'],
                ['label' => 'Asignaciones', 'path' => '/asignaciones-docentes'],
            ],
        ],
        [
            'title' => 'Estudiantes',
            'items' => [
                ['label' => 'Estudiantes', 'path' => '/estudiantes'],
                ['label' => 'Responsables', 'path' => '/responsables'],
                ['label' => 'Matriculas', 'path' => '/matriculas'],
            ],
        ],
        [
            'title' => 'Reportes',
            'items' => [
                ['label' => 'Reportes', 'path' => '/reportes'],
            ],
        ],
    ],
    'Director' => [
        [
            'title' => 'Panel',
            'items' => [
                ['label' => 'Panel director', 'path' => '/director/dashboard'],
            ],
        ],
        [
            'title' => 'Consultas',
            'items' => [
                ['label' => 'Reportes', 'path' => '/reportes'],
            ],
        ],
    ],
    'Secretario' => [
        [
            'title' => 'Panel',
            'items' => [
                ['label' => 'Panel secretario', 'path' => '/secretario/dashboard'],
            ],
        ],
        [
            'title' => 'Registro',
            'items' => [
                ['label' => 'Estudiantes', 'path' => '/estudiantes'],
                ['label' => 'Responsables', 'path' => '/responsables'],
                ['label' => 'Matriculas', 'path' => '/matriculas'],
            ],
        ],
        [
            'title' => 'Reportes',
            'items' => [
                ['label' => 'Reportes', 'path' => '/reportes'],
            ],
        ],
    ],
    'Profesor' => [
        [
            'title' => 'Panel',
            'items' => [
                ['label' => 'Panel profesor', 'path' => '/profesor/dashboard'],
            ],
        ],
        [
            'title' => 'Docencia',
            'items' => [
                ['label' => 'Mis materias', 'path' => '/profesor/materias'],
                ['label' => 'Evaluaciones', 'path' => '/profesor/evaluaciones'],
                ['label' => 'Notas', 'path' => '/profesor/notas'],
            ],
        ],
    ],
];

$role = $authUser['nombre_rol'] ?? '';
$roleGroups = $groups[$role] ?? [];
?>

<aside class="app-sidebar">
    <div class="sidebar-brand">
        <span class="brand-mark">CB</span>
        <div>
            <strong>ColdBend</strong>
            <small>Sistema academico</small>
        </div>
    </div>

    <nav class="sidebar-nav accordion-nav">
        <?php foreach ($roleGroups as $index => $group): ?>
            <?php
            $paths = array_column($group['items'], 'path');
            $groupState = is_active_group($paths);
            $isOpen = $groupState !== '';
            $buttonState = $isOpen ? '' : 'collapsed';
            $panelId = 'sidebar-group-' . $index;
            ?>
            <section class="sidebar-group <?= e($groupState) ?>">
                <button class="sidebar-group-toggle <?= e($buttonState) ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= e($panelId) ?>" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" aria-controls="<?= e($panelId) ?>">
                    <span><?= e($group['title']) ?></span>
                    <span class="sidebar-chevron">▾</span>
                </button>
                <div id="<?= e($panelId) ?>" class="sidebar-group-panel collapse <?= $isOpen ? 'show' : '' ?>">
                    <?php foreach ($group['items'] as $item): ?>
                        <a href="<?= e(url($item['path'])) ?>" class="<?= e(is_active($item['path'])) ?>">
                            <?= e($item['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </nav>
</aside>
