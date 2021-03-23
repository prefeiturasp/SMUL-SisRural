<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\SituacaoEnum;
use App\Enums\TipoPerguntaEnum;
use Auth;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário da Pergunta (Template do Formulário)
 */
class PerguntaForm extends Form
{
    public function buildForm()
    {
        $editable = true;
        $editableTipoPergunta = true;

        if ($this->model) {
            //Caso a pergunta já tenha sido utilizada em um formulário (finalizado ou resposta na unidade produtiva), não permite mais alterar
            $editable = Auth::user()->can('editForm', $this->model);

            //Caso já tenha sido cadastrado alguma resposta na pergunta, não é possível alterar o seu TIPO
            $editableTipoPergunta = Auth::user()->can('editTipoPergunta', $this->model);
        }

        //Normaliza os dados
        $editableReadonly = $editable ? null : 'readonly';
        $editableTipoPerguntaReadonly = $editable && $editableTipoPergunta ? null : 'readonly';

        $this->add('card-start', 'card-start', [
            'title' => 'Informações da pergunta',
            'titleTag' => 'h1'
        ]);

        /**
         * Tipo da pergunta (TipoPerguntaEnum)
         */
        $this->add(
            'tipo_pergunta',
            'select',
            [
                'label' => 'Tipo de pergunta',
                'choices' => TipoPerguntaEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Tipo']),
                'attr' => [
                    'readonly' => $editableTipoPerguntaReadonly
                ]
            ]
        );

        /**
         * Bloco da Tabela
         */
        $this->add('fieldset_start_tabela', 'fieldset-start', [
            'id' => 'card-tabela'
        ])->add('tabela_colunas', 'text', [
            'label' => 'Colunas da tabela',
            'attr' => [
                'data-role' => $editableReadonly ? null : 'tagsinput',
                'readonly' => $editableReadonly
            ],
            'help_block' => [
                'text' => 'Insira as palavras, separando por vírgulas'
            ],
        ])->add('tabela_linhas', 'text', [
            'label' => 'Linhas da tabela',
            'attr' => [
                'data-role' => $editableReadonly ?  null : 'tagsinput',
                'readonly' => $editableReadonly
            ],
            'help_block' => [
                'text' => 'Se for adicionado linhas, a tabela será fixa.<br>A primeira palavra fica no mesmo nível dos títulos das colunas.<br>Insira as palavras, separando por vírgulas'
            ],
        ])->add('tabela_preview', 'static', [
            'label' => false,
            'value' => 'Visualizar como vai ficar a tabela',
            'attr' => [
                'class' => 'btn btn-outline-primary btn-sm mb-2 mt-n4'
            ]
        ]);

        /**
         * Dados da Pergunta
         */
        $this->add('fieldset_end_tabela', 'fieldset-end')
            ->add('pergunta', 'textarea', [
                'label' => 'Pergunta',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Pergunta']),
                'attr' => [
                    'rows' => 2,
                    'readonly' => $editableReadonly
                ],
            ])->add('texto_apoio', 'textarea', [
                'label' => 'Texto de apoio',
                'attr' => [
                    'rows' => 2
                ],
            ])->add('plano_acao_default', 'textarea', [
                'label' => 'Ação recomendada, caso tenha plano de ação',
                'attr' => [
                    'rows' => 2
                ],
            ])
            ->add('tags', 'text', [
                'label' => 'Palavras-chave',
                'error' => __('validation.required', ['attribute' => 'Palavras-chave']),
                'attr' => [
                    'data-role' => 'tagsinput'
                ],
                'help_block' => [
                    'text' => 'Insira as palavras, separando por vírgulas'
                ],
            ])->add(
                'fl_arquivada',
                'select',
                [
                    'label' => 'Situação',
                    'choices' => SituacaoEnum::toSelectArray(),
                    'default_value' => SituacaoEnum::Ativa,
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Situação']),
                    'help_block' => [
                        'text' => 'Pergunta arquivada não aparece na criação de novos formulários. Na aplicação de formulários, ela aparece.'
                    ],
                ]
            );

        $this->add('card-end', 'card-end', []);

        $this->add('custom-redirect', 'hidden');
    }
}
