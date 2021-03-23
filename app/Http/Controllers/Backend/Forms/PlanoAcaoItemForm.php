<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do Item do Plano de Ação
 *
 * Esse formulário é utilizado pelo:
 *  - PDA Individual/Formulário
 *  - PDA Coletivo
 */
class PlanoAcaoItemForm extends Form
{
    public function buildForm()
    {
        // Caso tenha uma pergunta vinculada (PDA com Formulário), mostra o acompanhamento "default"
        if (@$this->model && @$this->model->checklist_pergunta) {
            $this->add('plano_acao_default', 'static', [
                'label' => 'Ação recomendada',
                'tag' => 'b',
                'value' => $this->model->checklist_pergunta->pergunta->plano_acao_default
            ]);
        }

        $this->add('descricao', 'textarea', [
            'label' => 'Descrição da ação',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Descrição']),
            'attr' => [
                'rows' => 2,
                'readonly' => $this->model ? (\Auth::user()->can('editDescricao', $this->model) ? null : 'readonly') : null
            ],
        ])->add(
            'status',
            'select',
            [
                'label' => 'Status da ação',
                'choices' => PlanoAcaoItemStatusEnum::toSelectArray(),
                'default_value' => PlanoAcaoItemStatusEnum::NaoIniciado,
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status']),
            ]
        )->add(
            'prioridade',
            'select',
            [
                'label' => 'Prioridade',
                'choices' => PlanoAcaoPrioridadeEnum::toSelectArray(),
                'default_value' => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica,
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Prioridade']),
            ]
        )->add('prazo', 'date', [
            'label' => 'Prazo da ação',
            'default_value' => @!$this->model || !$this->model->id ? !@$this->data['planoAcao']->prazo : null,
            'help_block' => [
                'text' => 'Formato da data: Dia/Mês/Ano. Ex: 31/12/2000',
                'tag' => 'b'
            ],
        ]);

        // No primeiro cadastro, permite inserir um acompanhamento inicial (depois é via tabela)
        if (@!$this->model || !$this->model->id) {
            $this->add('observacao', 'text', [
                'label' => 'Acompanhamento Inicial',
            ]);
        }
    }
}
