<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário da Unidade Operacional
 */
class DadoForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Dados Básicos',
            'titleTag' => 'h1'
        ])->add('nome', 'text', [
            'label' => 'Nome',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ])->add('api_token', 'text', [
            'label' => 'Token',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Token']),
            'help_block' => [
                'text' => 'IMPORTANTE: O valor digitado será encriptado. Para acessar a API o token utilizado deve ser a chave digitada.',
            ]
        ])->add('card-end', 'card-end', []);


        $whereInMunicipal = @$this->getRequest()->old()['abrangenciaMunicipal'];
        if ($whereInMunicipal) {
            $abrangenciaMunicipal = \App\Models\Core\CidadeModel::whereIn('id', $whereInMunicipal)->pluck('nome', 'id')->sortBy('nome')->toArray();
        } else {
            $abrangenciaMunicipal = (@$this->model->abrangenciaMunicipal) ? \App\Models\Core\CidadeModel::whereIn('id', @$this->model->abrangenciaMunicipal->pluck('id'))->pluck('nome', 'id')->sortBy('nome')->toArray() : [];
        }

        /**
         * Bloco p/ manipular as "abrangências" (regional, estadual, municipal)
         */
        $this->add('card-dados-start', 'card-start', [
            'title' => 'Abrangências',
            'titleTag' => 'h2'
        ])->add(
            'abrangenciaEstadual',
            'select',
            [
                'label' => 'Abrangência - Estadual',
                'choices' => \App\Models\Core\EstadoModel::orderByRaw('FIELD(uf, "SP") DESC, nome')->pluck('nome', 'id')->toArray(),
                'attr' => [
                    'multiple' => 'multiple',
                ]
            ]
        )->add(
            'abrangenciaMunicipal',
            'select',
            [
                'label' => 'Abrangência - Municipal',
                'choices' => @$abrangenciaMunicipal,
                'attr' => [
                    'multiple' => 'multiple',
                ]
            ]
        )->add(
            'regioes',
            'select',
            [
                'label' => 'Abrangência - Regiões',
                'choices' => \App\Models\Core\RegiaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'attr' => [
                    'multiple' => 'multiple',
                ]
            ]
        )->add('card-dados-end', 'card-end', []);
    }
}
