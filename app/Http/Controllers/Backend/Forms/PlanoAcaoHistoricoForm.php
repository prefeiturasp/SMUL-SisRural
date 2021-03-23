<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do histórico de um PDA (utilizado pelo PDA individual/formulário e coletivo)
 */
class PlanoAcaoHistoricoForm extends Form
{
    public function buildForm()
    {
        $this->add('texto', 'textarea', [
            'label' => 'Acompanhamento',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Acompanhamento']),
            'attr' => [
                'rows' => 2
            ],
        ]);
    }
}
