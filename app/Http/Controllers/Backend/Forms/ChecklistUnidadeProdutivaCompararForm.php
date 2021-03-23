<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\ChecklistStatusEnum;
use App\Models\Core\ChecklistModel;
use App\Models\Core\UnidadeProdutivaModel;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário p/ Comparação de um Formulário Aplicado
 *
 * N formulários
 * N unidades produtivas
 * UM status
 * Data Inicial
 * Data Final
 *
 */
class ChecklistUnidadeProdutivaCompararForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start-status', 'card-start', [
            'title' => 'Informações Gerais', 'titleTag' => 'h1'
        ])->add(
            'checklists',
            'select',
            [
                'label' => 'Formulário',
                'choices' => ChecklistModel::pluck('nome', 'id')->sortBy('name')->toArray(),
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Formulário']),
                'attr' => [
                    'multiple' => 'multiple',
                ],
                'help_block' => [
                    'text' => 'É possível selecionar mais de um formulário para comparação.',
                ]
            ]
        )->add(
            'unidades_produtivas',
            'select',
            [
                'label' => 'Unidade(s) Produtiva(s)',
                'choices' => UnidadeProdutivaModel::pluck('nome', 'id')->sortBy('name')->toArray(),
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Unidade Produtiva']),
                'attr' => [
                    'multiple' => 'multiple',
                ],
            ]
        )->add(
            'status',
            'select',
            [
                'label' => 'Status',
                'empty_value' => 'Selecione',
                'choices' => ChecklistStatusEnum::toSelectArray(),
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status']),
            ]
        )->add(
            'data_inicial',
            'date',
            [
                'rules' => 'required|date',
                'error' => __('validation.required', ['attribute' => 'Data Inicial']),
                'label' => 'Data Inicial',
            ]
        )->add(
            'data_final',
            'date',
            [
                'rules' => 'required|date|after:data_inicial',
                'error' => __('validation.required', ['attribute' => 'Data Final']),
                'label' => 'Data Final',
            ]
        )->add('card-end-status', 'card-end', []);
    }
}
