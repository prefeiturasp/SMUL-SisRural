<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Models\Core\DominioModel;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário da Unidade Operacional
 */
class UnidadeOperacionalForm extends Form
{
    public function buildForm()
    {
        /**
         * Bloco dos dados básicos da Unidade Operacional
         */
        $this->add('card-start', 'card-start', [
            'title' => 'Dados Básicos',
            'titleTag' => 'h1'
        ])->add('nome', 'text', [
            'label' => 'Nome',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ])->add(
            'dominio_id',
            'select',
            [
                'label' => 'Domínio',
                'choices' => \App\Models\Core\DominioModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Domínio'])
            ]
        )->add('endereco', 'text', [
            'label' => 'Endereço',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Endereço'])
        ])->add('telefone', 'text', [
            'label' => 'Telefone',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Telefone']),

            'attr' => [
                '_mask' => '99 99999999?9',
            ],
        ])->add('card-end', 'card-end', []);


        /**
         * Bloco p/ visualizar as "abrangências" (regional, estadual, municipal) do Domínio da Unidade Operacional
         *
         * É apenas informativo, serve como auxilio p/ o cadastro das abrangências da unidade operacional
         */
        $whereInMunicipal = @$this->getRequest()->old()['abrangenciaMunicipal'];
        if ($whereInMunicipal) {
            $abrangenciaMunicipal = \App\Models\Core\CidadeModel::whereIn('id', $whereInMunicipal)->pluck('nome', 'id')->sortBy('nome')->toArray();
        } else {
            $abrangenciaMunicipal = (@$this->model->abrangenciaMunicipal) ? \App\Models\Core\CidadeModel::whereIn('id', @$this->model->abrangenciaMunicipal->pluck('id'))->pluck('nome', 'id')->sortBy('nome')->toArray() : [];
        }

        $dominios = DominioModel::all();
        foreach ($dominios as $k => $dominio) {
            if (count($dominio->abrangenciaEstadual) > 0 || count($dominio->abrangenciaMunicipal) > 0 || count($dominio->abrangenciaRegional) > 0) {
                $this->add(
                    'card-dominio-start-' . $k,
                    'card-start',
                    [
                        'title' => 'Abrangências do Domínio - ' . $dominio->nome, 'id' => 'card-dominio-' . $dominio->id,
                        'titleTag' => 'h2'
                    ]
                );

                if (count($dominio->abrangenciaEstadual) > 0) {
                    $this->add('dominioEstadual-' . $k, 'static', [
                        'label' => 'Abrangência - Estadual',
                        'value' => join(", ", $dominio->abrangenciaEstadual->pluck('nome')->toArray()),
                        'tag' => 'b'
                    ]);
                }

                if (count($dominio->abrangenciaMunicipal) > 0) {
                    $this->add('dominioMunicipal-' . $k, 'static', [
                        'label' => 'Abrangência - Municipal',
                        'value' => join(", ", $dominio->abrangenciaMunicipal->pluck('nome')->toArray()),
                        'tag' => 'b'
                    ]);
                }

                if (count($dominio->abrangenciaRegional) > 0) {
                    $this->add('dominioRegional-' . $k, 'static', [
                        'label' => 'Abrangência - Estadual',
                        'value' => join(", ", $dominio->abrangenciaRegional->pluck('nome')->toArray()),
                        'tag' => 'b'
                    ]);
                }
                $this->add('card-dominio-end-' . $k, 'card-end');
            }
        }

        /**
         * Bloco p/ manipular as "abrangências" (regional, estadual, municipal)
         *
         * É apenas informativo
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
        )->add(
            'unidadesProdutivasManuais',
            'select',
            [
                'label' => 'Abrangência - Unidades Produtivas',
                'choices' => \App\Models\Core\UnidadeProdutivaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'attr' => [
                    'multiple' => 'multiple',
                ],
                'help_block' => [
                    'text' => "A Unidade Produtiva não será adicionada individualmente caso ela já pertença ao Estado, Município ou Região."
                ],
            ]
        )->add('card-dados-end', 'card-end', []);
    }
}
