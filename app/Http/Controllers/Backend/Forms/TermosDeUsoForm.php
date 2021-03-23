<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário dos Termos de Uso
 */
class TermosDeUsoForm extends Form
{
    public function buildForm()
    {
        $this->add('texto', 'hidden');
    }
}
