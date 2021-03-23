<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do histórico do item do PDA
 */
class PlanoAcaoItemHistoricoForm extends Form
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
