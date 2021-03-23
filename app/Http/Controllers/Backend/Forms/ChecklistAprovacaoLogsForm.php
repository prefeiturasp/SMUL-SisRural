<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário Aplicado que possui fluxo de aprovação e foi "finalizado" precisa ser "Aprovado/Reprovado"

 * Formulário p/ cadastrar a Análise de um Formulário aplicado
 *
 * É possível adicionar uma observação é qual o status (Aprovado, reprovado ou aguardando revisão (retorna p/ o usuário revisar)).
 *
 */
class ChecklistAprovacaoLogsForm extends Form
{
    public function buildForm()
    {
        $this->add(
            'status',
            'select',
            [
                'label' => 'Resultado da análise',
                'choices' => @$this->data['status'],
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status'])
            ]
        )->add('message', 'textarea', [
            'label' => 'Observação',
            'attr' => [
                'rows' => 1
            ],
        ]);
    }
}
