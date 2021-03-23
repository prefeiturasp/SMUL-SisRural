<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\BooleanEnum;
use App\Enums\CorEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\TipoPerguntaEnum;
use App\Enums\TipoPontuacaoEnum;
use Auth;
use Kris\LaravelFormBuilder\Form;

/**
 * Perguntas vinculadas a um Template de Formulário (ChecklistModel)
 */
class ChecklistPerguntaForm extends Form
{
    public function buildForm()
    {
        /**
         * Bloqueia a edição caso possa mais editar //Regras no ChecklistPerguntaPolicy
         */
        $editableReadonly = @$this->data['checklistPergunta'] && !Auth::user()->can('editForm', $this->data['checklistPergunta']) ? 'readonly' : null;

        $checklistCategoria = $this->data['checklistCategoria'];
        $pergunta = $this->data['pergunta'];

        $this->add(
            'pergunta_id',
            'hidden'
        )->add(
            'categoria',
            'static',
            [
                'label' => 'Categoria',
                'value' => $checklistCategoria->nome,
                'tag' => 'b'
            ]
        )->add(
            'pergunta',
            'static',
            [
                'label' => 'Pergunta',
                'value' => $pergunta->pergunta,
                'tag' => 'b'
            ]
        );

        /**
         * No momento de salvar com o status "finalizado" é verificado se a pergunta obrigatória foi ou não respondida
         */
        $this->add(
            'fl_obrigatorio',
            'select',
            [
                'label' => 'Pergunta obrigatória',
                'choices' => BooleanEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Pergunta obrigatória']),
            ]
        );

        $isChecklistComPontuacao = in_array($checklistCategoria->checklist->tipo_pontuacao, [TipoPontuacaoEnum::ComPontuacao, TipoPontuacaoEnum::ComPontuacaoFormulaPersonalizada]);
        $editableReadonlyPerguntas =  $isChecklistComPontuacao ? $editableReadonly : 'readonly';
        /**
         * Pergunta do tipo NumericaPontuacao, essa pergunta possui um "peso", utilizado no calculo de pontuação em um formulário aplicado.
         */
        if ($pergunta->tipo_pergunta == TipoPerguntaEnum::NumericaPontuacao) {
            $this->add('peso_pergunta', 'text', [
                'label' => 'Peso da Pergunta',
                'rules' => 'numeric',
                'attr' => [
                    'step' => 'any',
                    'readonly' => $editableReadonlyPerguntas
                ],
                'wrapper' => [
                    'id' => 'card-peso'
                ],
            ]);
        }

        /**
         * Tipos a seguir possuem "peso", utilizado no calculo de pontuação em um formulário aplicado.
         *
         * Perguntas de "cor" = "cinza" não possuem peso, elas são especiais (ignoradas no calculo de pontuação)
         */
        if ($pergunta->tipo_pergunta == TipoPerguntaEnum::Semaforica || $pergunta->tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $pergunta->tipo_pergunta == TipoPerguntaEnum::Binaria || $pergunta->tipo_pergunta == TipoPerguntaEnum::BinariaCinza  || $pergunta->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao || $pergunta->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
            foreach ($pergunta->respostas as $k => $v) {
                $descricao = $v->descricao;
                if (@$v->cor) {
                    $descricao = $descricao . ' (' . CorEnum::toSelectArray()[$v->cor] . ')';
                }

                if ($v->cor == 'cinza') {
                    if ($isChecklistComPontuacao) {
                        $this->add(
                            '' . $v['id'],
                            'static',
                            [
                                'label' => 'Peso da resposta - ' . $descricao,
                                'value' => 'Não há peso',
                            ]
                        );
                    }
                } else {
                    $this->add(
                        '' . $v['id'],
                        'number',
                        [
                            'label' => 'Peso da resposta - ' . $descricao,
                            'rules' => 'numeric',
                            'attr' => [
                                'readonly' => $editableReadonlyPerguntas,
                                'step' => 'any'
                            ],
                        ]
                    );
                }
            }
        }

        if ($checklistCategoria->checklist->plano_acao != PlanoAcaoEnum::NaoCriar) {
            /**
             * Bloco do PDA
             *
             * Determina se a pergunta entra no PDA, qual a ação default para ela e a prioridade.
             */
            $this->add(
                'fl_plano_acao',
                'select',
                [
                    'label' => 'Pergunta vai para o plano de ação?',
                    'choices' => BooleanEnum::toSelectArray(),
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Plano de ação']),
                    'attr' => [
                        'readonly' => $editableReadonly
                    ],
                ]
            )
                ->add('fieldset-plano-acao', 'fieldset-start', [
                    'id' => 'card-plano-acao'
                ])
                ->add(
                    'plano_acao_default',
                    'static',
                    [
                        'label' => 'Ação recomendada para o plano de ação',
                        'value' => $pergunta->plano_acao_default ? $pergunta->plano_acao_default : 'Não tem',
                        'tag' => 'b'
                    ]
                );


            $planoAcaoPrioridadeEnum = PlanoAcaoPrioridadeEnum::toSelectArray();
            unset($planoAcaoPrioridadeEnum['atendida']);

            $this->add(
                'plano_acao_prioridade',
                'select',
                [
                    'label' => 'Qual a prioridade no plano de ação?',
                    'choices' => $planoAcaoPrioridadeEnum,
                    'empty_value' => 'Selecione',
                ]
            )
                ->add('fieldset-plano-acao-end', 'fieldset-end');
        }
    }
}
