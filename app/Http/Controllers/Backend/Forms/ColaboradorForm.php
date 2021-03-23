<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário - Pessoas/Colaborador na Unidade Produtiva
 */
class ColaboradorForm extends Form
{
    public function buildForm()
    {
        $this->add('nome', 'text', [
            'label' => 'Nome Completo',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ])->add(
            'unidade_produtiva_id',
            'hidden',
            ['label' => 'Unidade Produtiva']
        )->add(
            'relacao_id',
            'select',
            [
                'label' => 'Relação',
                'choices' => \App\Models\Core\RelacaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Relação'])
            ]
        )->add('cpf', 'text', [
            'label' => 'CPF (Cadastro de pessoa física)',
            'attr' => [
                'placeholder' => 'CPF (Para certificação coletiva)',
                'class' => 'form-control req-cpf',
                '_mask' => '999.999.999-99',
            ],
            'wrapper' => [
                'id' => 'card-cpf'
            ],
            'error' => 'CPF inválido'
        ])->add('funcao', 'text', [
            'label' => 'Função',
        ])->add(
            'dedicacao_id',
            'select',
            [
                'label' => 'Dedicação',
                'choices' => \App\Models\Core\DedicacaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Dedicação'])
            ]
        );
    }
}
