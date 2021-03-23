<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Unidades produtivas vinculadas ao produtor
 */
class ProdutorUnidadeProdutivaSemUnidadeForm extends Form
{
    public function buildForm()
    {
        $this->add(
            'unidade_produtiva_id',
            'select',
            [
                'label' => 'Nome da Propriedade',
                'choices' => \App\Models\Core\UnidadeProdutivaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Nome da Propriedade'])
            ]
        )->add(
            'tipo_posse_id',
            'select',
            [
                'label' => 'Tipo de Relação',
                'choices' => \App\Models\Core\TipoPosseModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Tipo de Relação'])
            ]
        )->add('produtor_id', 'hidden');
    }
}
