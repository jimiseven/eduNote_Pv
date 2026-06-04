<?php

namespace App\Controllers\Academic;

use App\Core\Controller;
use App\Models\ConfiguracionSistema;
use App\Models\Gestion;

class ConfiguracionController extends Controller
{
    private ConfiguracionSistema $configuracion;
    private Gestion $gestiones;

    public function __construct() { $this->configuracion = new ConfiguracionSistema(); $this->gestiones = new Gestion(); }

    public function edit(): void
    {
        $user = $this->requireRole('Administrador Colegio');
        $this->view('academic/configuracion/edit', [
            'title' => 'Configuracion Academica',
            'configuracion' => $this->configuracion->findByColegio((int) $user['id_colegio']),
            'gestiones' => $this->gestiones->all((int) $user['id_colegio']),
            'errors' => $_SESSION['form_errors'] ?? [],
            'success' => flash('success'),
        ]);
        unset($_SESSION['form_errors']);
    }

    public function update(): void
    {
        $user = $this->requireRole('Administrador Colegio'); $this->verifyCsrf();
        $errors = [];
        if ((int) ($_POST['cantidad_periodos'] ?? 0) <= 0) { $errors[] = 'La cantidad de periodos debe ser mayor a cero.'; }
        if ((float) ($_POST['escala_nota_minima'] ?? 0) < 0 || (float) ($_POST['escala_nota_maxima'] ?? 100) > 100) { $errors[] = 'La escala debe estar entre 0 y 100.'; }
        if ($errors !== []) { $_SESSION['form_errors'] = $errors; $this->redirect('/configuracion-academica'); }
        $this->configuracion->save((int) $user['id_colegio'], $_POST); flash('success', 'Configuracion actualizada correctamente.'); $this->redirect('/configuracion-academica');
    }
}
