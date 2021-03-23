<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário de Infra-estrutura utilizado na Unidade Produtiva
 */
class InstalacoesForm extends Form
{
    public function buildForm()
    {
        $this->add(
            'instalacao_tipo_id',
            'select',
            [
                'label' => 'Tipo',
                'choices' => \App\Models\Core\InstalacaoTipoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Tipo'])
            ]
        )->add(
            'unidade_produtiva_id',
            'hidden',
            ['label' => 'Unidade Produtiva']
        )->add('descricao', 'textarea', [
            'label' => 'Descrição',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Descrição']),
            'attr' => [
                'rows' => 2
            ],
        ])->add('quantidade', 'number', [
            'label' => 'Quantidade',
            'attr' => [
                'step' => 'any'
            ]
        ])->add('area', 'number', [
            'wrapper' => ['class' => 'form-group row card-area'],
            'label' => 'Área (Hectares)',
            'attr' => [
                'step' => 'any'
            ]
        ])->add('observacao', 'textarea', [
            'label' => 'Observação',
            'attr' => [
                'rows' => 2
            ],
        ]);
    }
}
