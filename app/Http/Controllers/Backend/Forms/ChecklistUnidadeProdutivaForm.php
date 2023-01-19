<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\ChecklistStatusEnum;
use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\TipoPerguntaEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Aplicação de um Formulário
 */
class ChecklistUnidadeProdutivaForm extends Form
{
    public function buildForm()
    {
        $checklist = @$this->data['checklist'];
        $produtor = @$this->data['produtor'];
        $unidadeProdutiva = @$this->data['unidadeProdutiva'];

        /**
         * Dados gerais, apenas para visualização
         */
        if (@$produtor && @$unidadeProdutiva) {
            $this->add('card-start', 'card-start', [
                'title' => 'Informações do formulário',
                'titleTag' => 'h1'
            ])->add('checklist', 'static', [
                'label' => 'Nome do formulário',
                'tag' => 'b',
                'value' => $checklist['nome']
            ])->add('produtor', 'static', [
                'label' => 'Produtor/a',
                'tag' => 'b',
                'value' => $produtor['nome']
            ]);

            if (@$unidadeProdutiva['socios']) {
                $this->add('socios', 'static', [
                    'label' => 'Coproprietários/as',
                    'tag' => 'b',
                    'value' => $unidadeProdutiva['socios']
                ]);
            }

            $this->add('unidadeProdutiva', 'static', [
                'label' => 'Unidade Produtiva',
                'tag' => 'b',
                'value' => $unidadeProdutiva['nome']
            ])->add('tecnico', 'static', [
                'label' => 'Técnico/a',
                'tag' => 'b',
                'value' => @$this->data['usuario'] ? $this->data['usuario']->first_name . ' ' . $this->data['usuario']->last_name : auth()->user()->first_name . ' ' . auth()->user()->last_name
            ])->add('instrucoes', 'static', [
                'label' => 'Instruções Gerais',
                'tag' => 'span',
                'value' => @$checklist['instrucoes'] ?  $checklist['instrucoes'] : 'Não há'
            ])->add('card-end', 'card-end', []);
        } else {
            // utilizado no ChecklistController p/ visulizar o Template
            $this->add('card-start', 'card-start', [
                'title' => 'Exemplo do Formulário Aplicado',
                'titleTag' => 'h2'
            ])->add('checklist', 'static', [
                'label' => 'Formulário',
                'tag' => 'b',
                'value' => @$checklist['nome']
            ])->add('card-end', 'card-end', []);
        }

        /**
         * Caso seja uma edição ou o "segundo step" de uma aplicação de formulário, libera o bloco de "Categorias"
         *
         * O bloco de "Categorias" lista todas as "Perguntas" junto com as "Respostas"
         *
         * As "respostas" vem da "UnidadeProdutivaRespostas"
         *
         * Cada pergunta tem um tipo:
         *
         * - Semafórica
         * - Semafórica Cinza (Não se aplica)
         * - Binária
         * - Binária Cinza (Não se aplica)
         * - Numérica com Pontuação
         * - Numérica (sem pontuação)
         * - Texto
         * - Tabela (esse tipo de questão é montado através de JS, ver app.js -> input-tabela)
         * - Multipla Escolha
         * - Escolha Simples
         * - Escolha Simples com Pontuação
         * - Anexo
         */
        if ($checklist) {

            $itensUltimoPda = @$this->data['itensUltimoPda'];


            //Perguntas arquivadas ainda podem ser respondidas
            $categorias = $checklist->categorias()->with('perguntas', 'perguntas.respostas')->get();

            foreach ($categorias as $k => $categoria) {
                //Ignora categorias sem perguntas
                if (count($categoria->perguntas) == 0) {
                    continue;
                }

                $this->add('card-start-' . $categoria->id, 'card-start', [
                    'title' => $categoria->nome,
                    'titleTag' => 'h2'
                ]);

                foreach ($categoria->perguntas as $k => $v) { //checklist_pergunta

                    $helperTextPda = '';
                    if ($itensUltimoPda) {
                        $checklist_pergunta_id = $v->pivot->id;
                        $itemPda = @$itensUltimoPda[$checklist_pergunta_id];

                        if ($itemPda) {
                            $helperTextPda = 'Ação planejada anteriormente: ' . $itemPda->descricao . '<span class="text-primary"><br>Status: ' . PlanoAcaoItemStatusEnum::toSelectArray()[$itemPda->status] . '</span>';
                        } else {
                            $helperTextPda = 'Não tem.';
                        }
                    }

                    $tipo_pergunta = $v->tipo_pergunta;

                    $labelPergunta = $v->pergunta . ($v->pivot->fl_obrigatorio ? '*' : '');

                    $textoApoio = $v['texto_apoio'] . ($v['texto_apoio'] ? '<br>' . $helperTextPda : $helperTextPda);

                    if ($tipo_pergunta == TipoPerguntaEnum::Semaforica || $tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza || $tipo_pergunta == TipoPerguntaEnum::Binaria || $tipo_pergunta == TipoPerguntaEnum::BinariaCinza) {
                        $respostas = collect($v['respostas']);
                        $respostasColorAr = $respostas->pluck('cor', 'id')->toArray();
                        $respostasAr = $respostas->pluck('descricao', 'id')->toArray();

                        $this->add(
                            $v['id'],
                            'select',
                            [
                                'label' => $labelPergunta,
                                'choices' => $respostasAr,
                                'empty_value' => 'Selecione',
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                                'attr' => [
                                    'data-option-color' => join(",", $respostasColorAr),
                                ]
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::NumericaPontuacao || $tipo_pergunta == TipoPerguntaEnum::Numerica) {
                        $this->add(
                            $v['id'],
                            'number',
                            [
                                'label' => $labelPergunta,
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                                'attr' => [
                                    'step' => 'any'
                                ]
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::Texto) {
                        $this->add(
                            $v['id'],
                            'textarea',
                            [
                                'label' => $labelPergunta,
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                                'attr' => [
                                    'rows' => 2
                                ]
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::Data) {
                        $this->add(
                            $v['id'],
                            'date',
                            [
                                'label' => $labelPergunta,
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::Hora) {
                        $this->add(
                            $v['id'],
                            'time',
                            [
                                'label' => $labelPergunta,
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::MultiplaEscolha) {
                        $respostas = collect($v['respostas'])->pluck('descricao', 'id')->toArray();

                        $this->add(
                            $v['id'],
                            'select',
                            [
                                'label' => $labelPergunta,
                                'choices' => $respostas,
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                                'attr' => [
                                    'multiple' => 'multiple',
                                ],
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::EscolhaSimples || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao || $tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                        $respostas = collect($v['respostas'])->pluck('descricao', 'id')->toArray();

                        $this->add(
                            $v['id'],
                            'select',
                            [
                                'label' => $labelPergunta,
                                'choices' => $respostas,
                                'empty_value' => 'Selecione',
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::Tabela) {
                        $this->add(
                            $v['id'],
                            'text',
                            [
                                'label' => $labelPergunta,
                                'help_block' => [
                                    'text' => $textoApoio
                                ],
                                'attr' => [
                                    'class' => 'form-control input-tabela',
                                    'data-colunas' => $v['tabela_colunas'],
                                    'data-linhas' => $v['tabela_linhas']
                                ],
                            ]
                        );
                    } else if ($tipo_pergunta == TipoPerguntaEnum::Anexo) {
                        $upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
                        $this->add(
                            $v['id'],
                            'file',
                            [
                                'label' => $labelPergunta,
                                'rules' => 'max:' . $upload_max_filesize . '|mimes:doc,docx,pdf,ppt,pptx,xls,xlsx,png,jpg,jpeg,gif,txt,kml,shp', //required|
                                "maxlength" => $upload_max_filesize,
                                'help_block' => [
                                    'text' => $textoApoio . '<br>Tamanho máximo do arquivo: ' . ini_get('upload_max_filesize'),
                                ],
                            ]
                        );
                    }
                }

                $this->add('card-end-' . $categoria->id, 'card-end');
            }
        }

        $checklistStatusEnum = ChecklistStatusEnum::toSelectArray();
        unset($checklistStatusEnum['aguardando_pda']);
        unset($checklistStatusEnum['aguardando_aprovacao']);
        unset($checklistStatusEnum['cancelado']);

        $this->add('card-gallery', 'hidden');

        /**
         * Bloco de Status
         */
        $this->add('card-start-status', 'card-start', [
            'title' => 'Detalhes',
            'titleTag' => 'h2'
        ])->add(
            'status',
            'select',
            [
                'label' => 'Status',
                'choices' => $checklistStatusEnum,
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status'])
            ]
        );

        /**
         * Bloco de visualização p/ mostrar quando foi criado/atualizado
         */
        if (@$this->model['created_at']) {
            $this->add('created_at', 'static', [
                'label' => 'Criado em',
                'tag' => 'b',
                'value' => \App\Helpers\General\AppHelper::formatDate($this->model['created_at'])
            ])->add('updated_at', 'static', [
                'label' => 'Atualizado em',
                'tag' => 'b',
                'value' =>  \App\Helpers\General\AppHelper::formatDate($this->model['updated_at'])
            ]);
        }

        $this->add('card-end-status', 'card-end', []);

        $this->add('checklist_id', 'hidden')
            ->add('produtor_id', 'hidden')
            ->add('unidade_produtiva_id', 'hidden')
            ->add('redirect_pda', 'hidden')
            ->add('custom-redirect', 'hidden');
    }
}
