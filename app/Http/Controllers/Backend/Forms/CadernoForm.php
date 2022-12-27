<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\CadernoStatusEnum;
use App\Enums\TipoTemplatePerguntaEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do Caderno de Campo (aplicação)
 */
class CadernoForm extends Form
{
    public function buildForm()
    {

        // $produtor = null;
        // if ($this->model->produtor_id) {
        //     $produtor = \App\Models\Core\ProdutorModel::where('id', $this->model->produtor_id)->first();
        // }

        /**
         * Dados gerais (texto, sem edição), apenas para visualização
         */
        $produtor = $this->data['produtor'];
        $unidadeProdutiva = $this->data['unidadeProdutiva'];



        $this->add('card-start-1', 'card-start', [
            'title' => 'Informações ',
            'titleTag' => 'h1'
        ])->add('produtor', 'static', [
            'label' => 'Produtor/a',
            'tag' => 'b',
            'value' => $produtor['nome']
        ])->add('unidadeProdutiva', 'static', [
            'label' => 'Unidade Produtiva',
            'tag' => 'b',
            'value' => $unidadeProdutiva['nome']
        ])->add(
            'tecnicas',
            'select',
            [
                'label' => 'Técnicos/as',
                'choices' => \App\Models\Auth\User::orderByRaw('first_name DESC')->pluck('first_name', 'id')->toArray(),
                'attr' => [
                    'multiple' => 'multiple',
                ]
            ]
        )->add('card-end-1', 'card-end', []);


        /**
         * Start
         */
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
        ]);

        /**
         * Empilha as perguntas na tela, cada tipo de pergunta tem um "formato" de dado
         *
         * Texto livre - Text
         * Uma escolha - Check
         * Multipla escolha - MultipleCheck
         * Data - Date
         */
        $template = @$this->data['template'];
        if ($template) {
            //Ordena as perguntas
            $perguntas = $template->perguntas()->with(['respostas' => function ($query) {
                return $query->orderBy('ordem', 'ASC');
            }])->get()->toArray();

            foreach ($perguntas as $k => $v) {
                if ($v['tipo'] == TipoTemplatePerguntaEnum::Text) {
                    $this->add(
                        $v['id'],
                        'textarea',
                        [
                            'label' => $v['pergunta'],
                            'attr' => [
                                'rows' => 2
                            ]
                        ]
                    );
                } else if ($v['tipo'] == TipoTemplatePerguntaEnum::Check) {
                    $respostas = collect($v['respostas'])->pluck('descricao', 'id')->toArray();

                    $this->add(
                        $v['id'],
                        'select',
                        [
                            'label' => $v['pergunta'],
                            'choices' => $respostas,
                            'empty_value' => 'Selecione',
                        ]
                    );
                } else if ($v['tipo'] == TipoTemplatePerguntaEnum::MultipleCheck) {
                    $respostas = collect($v['respostas'])->pluck('descricao', 'id')->toArray();

                    $this->add(
                        $v['id'],
                        'select',
                        [
                            'label' => $v['pergunta'],
                            'choices' => $respostas,
                            'attr' => [
                                'multiple' => 'multiple',
                            ],
                        ]
                    );
                } else if ($v['tipo'] == TipoTemplatePerguntaEnum::Data) {
                    $this->add(
                        $v['id'],
                        'date',
                        [
                            'label' => $v['pergunta'],
                        ]
                    );
                } else if ($v['tipo'] == TipoTemplatePerguntaEnum::Hora) {
                    $this->add(
                        $v['id'],
                        'time',
                        [
                            'label' => $v['pergunta'],
                        ]
                    );
                }
            }
        }

        //Status do caderno de campo
        $this->add(
            'status',
            'select',
            [
                'label' => 'Status',
                'choices' => CadernoStatusEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status'])
            ]
        );

        $this->add('card-end', 'card-end', []);

        $this->add('custom-redirect', 'hidden')
            ->add('produtor_id', 'hidden')
            ->add('unidade_produtiva_id', 'hidden')
            ->add('template_id', 'hidden');
    }
}
