<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário para cadastro de "Uso do Solo" - Unidade Produtiva
 */
class UnidadeProdutivaCaracterizacaoForm extends Form
{
    public function buildForm()
    {
        $this->add(
            'unidade_produtiva_id',
            'hidden',
            ['label' => 'Unidade Produtiva']
        )->add(
            'solo_categoria_id',
            'select',
            [
                'label' => 'Categoria',
                'choices' => \App\Models\Core\SoloCategoriaModel::where('tipo', 'geral')->pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Categoria'])
            ]
        )->add('area', 'number', [
            'label' => 'Área (' . env('UNIDADE_MEDIDA_AREA_SIGLA') . ')',
            'wrapper' => ['class' => 'form-group row todos hectares'],
            'attr' => [
                'step' => 'any'
            ]
        ])->add('quantidade', 'number', [
            'label' => 'Quantidade de Espécies',
            'wrapper' => ['class' => 'form-group row todos']
        ])->add('descricao', 'textarea', [
            'label' => 'Descrição',
            'attr' => [
                'rows' => 4
            ],
            'wrapper' => ['class' => 'form-group row todos']
        ]);
    }
}
