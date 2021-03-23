<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\CorEnum;
use App\Enums\TipoPerguntaEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário das respostas de uma pergunta (PerguntaModel) utilizado em um template de formulário
 */
class RespostaForm extends Form
{
    public function buildForm()
    {
        //Não permite a edição caso a resposta já tenha sido utilizada (ou se a pergunta da resposta já tenha sido utilizada) em um formulário aplicado (ChecklistSnapshotRespostaModel ou UnidadeProdutivaRespostaModel)
        $editable = true;
        if (@$this->model && @$this->model->id) {
            $editable = \Auth::user()->can('editForm', $this->model);
        }

        $editableReadonly =  $editable ? null : 'readonly';

        $this->add('descricao', 'textarea', [
            'label' => 'Resposta',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Resposta']),
            'attr' => [
                'rows' => 2,
                'readonly' => $editableReadonly,
            ],
        ]);

        if ($this->data['tipo_pergunta'] == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
            $this->add(
                'cor',
                'select',
                [
                    'label' => 'Cor da resposta',
                    'choices' => ['cinza' => 'Cinza', 'verde' => 'Verde'],
                    'empty_value' => 'Nenhuma',
                    'error' => __('validation.required', ['attribute' => 'Resposta não se aplica']),
                    'attr' => [
                        'data-option-color' => join(",", CorEnum::getValues()),
                        'readonly' => $editableReadonly,
                    ],
                    'help_block' => [
                        'text' => 'Assinale, caso se aplique, <i>"Verde"</i> para a resposta correta, e <i>"Cinza"</i> para a resposta "Não se aplica".<br>Estas configurações implicam no Plano de Ação, caso haja. Para as demais respostas, mantenha em <i>"Nenhuma"</i>'
                    ],
                ]
            );
        }


        /**
         * Tipo SemaforicaCinza
         */
        if ($this->data['tipo_pergunta'] == TipoPerguntaEnum::SemaforicaCinza) {
            $this->add(
                'cor',
                'select',
                [
                    'label' => 'Cor',
                    'choices' => CorEnum::toSelectArray(),
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Cor']),
                    'attr' => [
                        'data-option-color' => join(",", CorEnum::getValues()),
                        'readonly' => $editableReadonly,
                    ]
                ]
            );
        }

        /**
         * Tipo Semaforica
         */
        if ($this->data['tipo_pergunta'] == TipoPerguntaEnum::Semaforica) {
            $cores = CorEnum::toSelectArray();
            unset($cores['cinza']);

            $this->add(
                'cor',
                'select',
                [
                    'label' => 'Cor',
                    'choices' => $cores,
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Cor']),
                    'attr' => [
                        'data-option-color' => join(",", array_keys($cores)),
                        'readonly' => $editableReadonly,
                    ]
                ]
            );
        }

        /**
         * Tipo Binaria
         */
        if ($this->data['tipo_pergunta'] == TipoPerguntaEnum::Binaria) {
            $cores = CorEnum::toSelectArray();
            unset($cores['cinza']);
            unset($cores['amarelo']);

            $this->add(
                'cor',
                'select',
                [
                    'label' => 'Cor',
                    'choices' => $cores,
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Cor']),
                    'attr' => [
                        'data-option-color' => join(",", array_keys($cores)),
                        'readonly' => $editableReadonly,
                    ]
                ]
            );
        }

        /**
         * Tipo BinarizaCinza
         */
        if ($this->data['tipo_pergunta'] == TipoPerguntaEnum::BinariaCinza) {
            $cores = CorEnum::toSelectArray();
            unset($cores['amarelo']);

            $this->add(
                'cor',
                'select',
                [
                    'label' => 'Cor',
                    'choices' => $cores,
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Cor']),
                    'attr' => [
                        'data-option-color' => join(",", array_keys($cores)),
                        'readonly' => $editableReadonly,
                    ]
                ]
            );
        }
    }
}
