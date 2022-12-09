<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\BooleanEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\TemplateChecklistStatusEnum;
use App\Enums\TipoPontuacaoEnum;
use App\Models\Auth\User;
use App\Models\Core\DominioModel;
use App\Models\Core\UnidadeOperacionalModel;
use Kris\LaravelFormBuilder\Form;

/**
 * Template do Formulário
 */
class ChecklistForm extends Form
{
    public function buildForm()
    {
        $canEditFormula = @!$this->model || !\Auth::user()->can('editFormula', $this->model) ? 'readonly' : null;

        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        /**
         * Domínio dono do formulário, essa informação é importante pois interage com os Policies e PermissionsScopes
         */
        $this->add('dominio_id', 'select', [
            'label' => 'Domínio responsável',
            'choices' => \App\Models\Core\DominioModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'empty_value' => 'Selecione',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Domínio']),
            'attr' => [
                'readonly' => $this->model ? 'readonly' : null
            ]
        ])->add('nome', 'text', [
            'label' => 'Nome do formulário',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome']),
        ])->add('instrucoes', 'textarea', [
            'label' => 'Instruções gerais', 'attr' => ['rows' => 5],
            'rules' => 'max:65000',
        ])->add(
            'fl_gallery',
            'select',
            [
                'label' => 'Possuí galeria?',
                'choices' => BooleanEnum::toSelectArray(),
                'empty_value' => 'Selecione',
            ]
        );

        $this->add('card-end', 'card-end', []);

        /**
         * Bloco das Permissões de aplicação (Domínio, Unidade Operacional ou Técnico)
         */
        $this->add('card-start-permissions', 'card-start', [
            'titleTag' => 'h2',
            'title' => 'Permissões para aplicação do formulário',
            'help_block' => [
                'text' => 'Selecione quais Domínios, Unidades Operacionais ou Usuários terão acesso ao Formulário.',
                'tag' => 'b'
            ],
        ])->add('usuarios', 'select', [
            'label' => 'Técnicos/as',
            'choices' => User::whereHas('roles', function ($q) {
                $q->where('name', 'Tecnico');
                $q->orWhere('name', 'Unidade Operacional');
            })->withoutGlobalScopes()->get()->pluck('full_name_dominio_role', 'id')->sortBy('full_name_dominio_role')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('dominios', 'select', [
            'label' => 'Domínios',
            'choices' => DominioModel::withoutGlobalScopes()->pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('unidadesOperacionais', 'select', [
            'label' => 'Unidades operacionais',
            'choices' => UnidadeOperacionalModel::withoutGlobalScopes()->pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ]);

        $this->add('card-end-permissions', 'card-end', []);


        /**
         * Bloco de Análise / Fluxo de Aprovação
         */
        $this->add('card-start-aprovacao', 'card-start', [
            'title' => 'Fluxo de aprovação',
            'titleTag' => 'h2'
        ])->add(
            'fl_fluxo_aprovacao',
            'select',
            [
                'label' => 'Possuí fluxo de aprovação?',
                'choices' => BooleanEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Possuí fluxo de aprovação']),
            ]
        )->add('card-fluxo-aprovacao-start', 'fieldset-start', [
            'id' => 'card-fluxo-aprovacao',
        ])->add('usuariosAprovacao', 'select', [
            'label' => 'Responsáveis pela aprovação',
            'choices' => User::whereHas('roles', function ($q) {
                $q->where('name', 'Tecnico');
                $q->orWhere('name', 'Unidade Operacional');
                $q->orWhere('name', 'Dominio');
            })->withoutGlobalScopes()->get()->sortBy('full_name_dominio_role')->pluck('full_name_dominio_role', 'id')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('card-fluxo-aprovacao-end', 'fieldset-end', []);
        $this->add('card-end-aprovacao', 'card-end', []);

        /**
         * Bloco do PDA
         */
        $this->add('card-start-pda', 'card-start', [
            'title' => 'Plano de ação',
            'titleTag' => 'h2'
        ])->add(
            'plano_acao',
            'select',
            [
                'label' => 'Tem plano de ação associado ao formulário?',
                'choices' => PlanoAcaoEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Plano de ação']),
            ]
        )->add('instrucoes_pda', 'textarea', [
            'label' => 'Instruções gerais do plano de ação',
            'attr' => ['rows' => 5],
            'rules' => 'max:65000',
        ])->add('card-end-pda', 'card-end', []);

        /**
         * Bloco da Pontuação
         */
        $this->add('card-start-pontuacao', 'card-start', [
            'title' => 'Pontuação do formulário',
            'titleTag' => 'h2'
        ])->add(
            'tipo_pontuacao',
            'select',
            [
                'label' => 'Tipo de Pontuação',
                'choices' => TipoPontuacaoEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'default_value' => TipoPontuacaoEnum::SemPontuacao,
                'error' => __('validation.required', ['attribute' => 'Tipo de Pontuação']),
                'help_block' => [
                    'text' => "1. Quando quiser criar formulário apenas para coleta de dados, escolha 'Sem pontuação'.<br>2. Para formulários que geram pontuação, escolha 'Com pontuação' para gerar nota média (0-100%), ou 'Com pontuação - Fórmula personalizada' para fórmula customizável."
                ],
            ]
        )->add('card-calculo-start', 'fieldset-start', [
            'id' => 'card-calculo',
        ])->add(
            'fl_nao_normalizar_percentual',
            'select',
            [
                'label' => 'Não Normalizar Percentual',
                'choices' => BooleanEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'help_block' => [
                    'text' => "O percentual da formula é normalizado entre 0 - 100%.<br>O peso mínimo do formulário aplicado pode ser diferente de zero (negativo ou maior que zero), por isso é feito a normalização.<br>Em alguns casos a normalização não é necessária, podendo ser desabilitada.<br>Base de cálculo para normalização (padrão): (pontuação do formulário aplicado - pontuação mínima possível) / (pontuação máxima possível - pontuação mínima possível)."
                ],
            ]
        )->add('card-calculo-end', 'fieldset-end')
            ->add('card-formula-start', 'fieldset-start', [
                'id' => 'card-formula',
            ])->add('formula', 'text', [
                'label' => 'Fórmula Personalizada',
                'error' => __('validation.required', ['attribute' => 'Fórmula']),
                'rules' => 'formula',
                'help_block' => [
                    'text' => "Para utilizar a pontuação da Categoria: C+código da categoria: <br>Ex: 100 * (C1 + C3)<br><br>O código da categoria pode ser visto logo abaixo, na listagem de categorias cadastradas.<br>Não é necessário colocar o '=' no início da fórmula.<br><br>Operadores: +, -, *, /, % <br><br>Funções: 'abs', 'acos', 'acosh', 'asin', 'asinh', 'atan2', 'atan', 'atanh', 'ceil', 'cos', 'cosh', 'exp', 'floor', 'fmod', 'hypot', 'intdiv', 'log10', 'log', 'pi', 'pow', 'round', 'sin', 'sinh', 'sqrt', 'tan', 'tanh', 'deg2rad'"
                ],
                'attr' => [
                    'readonly' => $canEditFormula
                ]
            ])->add('formula_prefix', 'text', [
                'label' => 'Prefixo da Fórmula',
                'help_block' => [
                    'text' => "O prefixo irá aparecer antes do resultado da fórmula.<br>Exemplo: R$"
                ],
                'attr' => [
                    'readonly' => $canEditFormula
                ]
            ])->add('formula_sufix', 'text', [
                'label' => 'Sufixo da Fórmula',
                'help_block' => [
                    'text' => "O sufixo irá aparecer após o resultado da fórmula.<br>Exemplo: %"
                ],
                'attr' => [
                    'readonly' => $canEditFormula
                ]
            ])->add('card-formula-end', 'fieldset-end')
            ->add('card-end-pontuacao', 'card-end', []);

        /**
         * Se é uma edição (ou next step de um formulário), libera o campo de "STATUS"
         *
         * O status define se esta em rascunho (ninguém consegue aplicar), inativo (ninguém consegue aplicar), publicado (pode ser utilizado para aplicar)
         */
        if ($this->model) {
            $status = TemplateChecklistStatusEnum::toSelectArray();
            if (!\Auth::user()->can('editStatus', $this->model)) {
                unset($status['rascunho']);
            }

            $this->add('card-start-status', 'card-start', [
                'title' => 'Detalhes',
                'titleTag' => 'h2'
            ])->add(
                'status',
                'select',
                [
                    'label' => 'Status',
                    'choices' => $status,
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Status']),
                    'help_block' => [
                        'text' => 'Só é possível aplicar um formulário quando o status for "Publicado".'
                    ],
                ]
            )->add('card-end-status', 'card-end', []);
        }

        $this->add('custom-redirect', 'hidden');
    }
}
