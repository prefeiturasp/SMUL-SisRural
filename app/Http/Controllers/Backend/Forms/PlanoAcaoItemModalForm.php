<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário utilizado no modal do plano de ação item -> ver "PlanoAcaoItemConChecklistTrait.php"
 */
class PlanoAcaoItemModalForm extends Form
{
    public function buildForm()
    {
        $this->add('plano_acao_default', 'static', [
            'label' => 'Ação recomendada',
            'tag' => 'b',
            'value' => $this->model->checklist_pergunta->pergunta->plano_acao_default
        ])->add('descricao', 'textarea', [
            'label' => 'Detalhamento da ação',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Detalhamento da ação']),
            'attr' => [
                'rows' => 2
            ],
        ])->add('prazo', 'date', [
            'label' => 'Prazo da ação',
            'default_value' => @$this->data['planoAcao']->prazo,
            'help_block' => [
                'text' => 'Formato da data: Dia/Mês/Ano. Ex: 31/12/2000',
                'tag' => 'b'
            ],
        ]);
    }
}
