<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário para Domínios
 */
class DominioForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        $this->add('nome', 'text', [
            'label' => 'Nome',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ])
            ->add('card-end', 'card-end', [])
            ->add('card-coverage-start', 'card-start', [
                'title' => 'Abrangências',
                'titleTag' => 'h2'
            ])
            ->add('helptext', 'static', [
                'label' => ' ',
                'value' => 'Caso nenhuma abrangência seja selecionada, o domínio será considerado de abrangência Nacional.',
                'tag' => 'b',
            ])
            ->add(
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
                    'choices' => (@$this->model->abrangenciaMunicipal) ? \App\Models\Core\CidadeModel::whereIn('id', @$this->model->abrangenciaMunicipal->pluck('id'))
                        ->pluck('nome', 'id')->sortBy('nome')->toArray() : [],
                    'attr' => [
                        'multiple' => 'multiple',
                    ]
                ]
            )->add(
                'abrangenciaRegional',
                'select',
                [
                    'label' => 'Abrangência - Regiões',
                    'choices' => \App\Models\Core\RegiaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                    'attr' => [
                        'multiple' => 'multiple',
                    ]
                ]
            );

        $this->add('card-coverage-end', 'card-end', []);
    }
}
