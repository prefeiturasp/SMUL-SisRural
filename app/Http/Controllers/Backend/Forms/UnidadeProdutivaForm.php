<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Models\Core\StatusAcompanhamentoModel;
use App\Enums\CheckboxEnum;
use App\Enums\ProcessaProducaoEnum;
use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use App\Enums\UnidadeProdutivaCarEnum;
use App\Helpers\General\AppHelper;
use App\Models\Core\CanalComercializacaoModel;
use App\Models\Core\CertificacaoModel;
use App\Models\Core\EsgotamentoSanitarioModel;
use App\Models\Core\OutorgaModel;
use App\Models\Core\PressaoSocialModel;
use App\Models\Core\ResiduoSolidoModel;
use App\Models\Core\RiscoContaminacaoAguaModel;
use App\Models\Core\SoloCategoriaModel;
use App\Models\Core\TipoFonteAguaModel;
use Kris\LaravelFormBuilder\Form;
use App\Enums\TipoPerguntaEnum;

/**
 * Formulário - Unidade Produtiva
 */
class UnidadeProdutivaForm extends Form
{
    public function buildForm()
    {
        /**
         * Bloco com informações sobre o produtor (Aparece quando for edição de uma unidade produtiva ou cadastro passando o id do produtor)
         */
        $produtor = @$this->data['produtor'];
        if (@$produtor) {
            $this->add('card-start-1', 'card-start', [
                'title' => 'Dados do/a Produtor/a',
                'titleTag' => 'h1'
            ])->add('produtor', 'static', [
                'label' => 'Produtor/a',
                'tag' => 'b',
                'value' => $produtor['nome']
            ])->add(
                'tipo_posse_id',
                'select',
                [
                    'label' => 'Tipo de Relação',
                    'choices' => \App\Models\Core\TipoPosseModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Tipo de Relação'])
                ]
            )->add(
                'produtor_id',
                'hidden'
            )->add(
                'owner_id',
                'hidden'
            )->add(
                'fl_fora_da_abrangencia_app',
                'hidden'
            )->add('card-end-1', 'card-end', []);
        } else if (isset($this->data['produtores']) && isset($this->data['produtores'])) {
            $this->add('card-start-pr', 'card-start', ['title' => 'Informações Gerais']);

            $this->add('produtor', 'static', [
                'label' => 'Produtores/as',
                'tag' => 'b',
                'value' => join(", ", $this->data['produtores']->pluck('nome')->toArray())
            ]);

            $this->add('card-end-pr', 'card-end');
        }

        /**
         * Bloco Dados Básicos
         */
        $this->add('card-start', 'card-start', [
            'title' => 'Dados Básicos',
        ])->add('nome', 'text', [
            'label' => 'Nome da Unidade Produtiva',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome da Unidade Produtiva'])
        // ])->add('cep', 'text', [
        //     'label' => 'CEP (Código de Endereçamento Postal)',            
        //     'attr' => [
        //         '_mask' => '99999-999',
        //     ],
        ])->add('endereco', 'text', [
            'label' => 'Endereço',
            'rules' => 'required',
        ])->add('bairro', 'text', [
            'label' => 'Bairro',
        ])->add('subprefeitura', 'select', [
            'label' => 'Distrito',
            'empty_value' => 'Selecione',
            'choices' => ['Maricá' => 'Maricá', 'Ponta Negra' => 'Ponta Negra', 'Inoã' => 'Inoã', 'Itaipuaçu' => 'Itaipuaçu'],
        ])->add(
            'estado_id',
            'select',
            [
                'label' => 'Estado',
                'empty_value' => 'Selecione',
                'choices' => \App\Models\Core\EstadoModel::orderByRaw('FIELD(uf, "SP") DESC, nome')->pluck('nome', 'id')->toArray(),
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Estado'])
            ]
        )->add(
            'cidade_id',
            'select',
            [
                'label' => 'Município',
                'empty_value' => 'Selecione',
                'choices' => @$this->model['estado_id'] ? \App\Models\Core\CidadeModel::where('estado_id', @$this->model['estado_id'])->pluck('nome', 'id')->sortBy('nome')->toArray() : [],
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Município'])
            ]
        // )->add('bacia_hidrografica', 'text', [
        //     'label' => 'Bacia Hidrográfica',
        //     ]
        )->add(
            'status',
            'select',
            [
                'label' => 'Status',
                'choices' => ProdutorUnidadeProdutivaStatusEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status']),
            ]
        )->add('status_observacao', 'text', [
            'label' => 'Status - Observação',
            ]
        )->add(
            'status_acompanhamento_id',
            'select',
            [
                'label' => 'Status do Acompanhamento',
                'choices' => StatusAcompanhamentoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status']),
            ]        
        )->add('card-dados-end', 'card-end');

        /**
         * Bloco Dados Checklist
         */

        if( isset($this->data['checklist']) && $this->data['checklist'] ){
            $checklist = $this->data['checklist'];
            $categorias = $checklist->categorias()->with('perguntas', 'perguntas.respostas')->get();

            foreach ($categorias as $k => $categoria) {
                //Ignora categorias sem perguntas
                if (count($categoria->perguntas) == 0) {
                    continue;
                }

                $this->add('card-start-' . $categoria->id, 'card-start', [
                    'title' => $categoria->nome . $checklist->id,
                    'titleTag' => 'h2'
                ]);

                foreach ($categoria->perguntas as $k => $v) { //checklist_pergunta
                    // if($k == 17){
                    //     print_r($v);
                    // }
                    // $helperTextPda = '';
                    // if ($itensUltimoPda) {
                    //     $checklist_pergunta_id = $v->pivot->id;
                    //     $itemPda = @$itensUltimoPda[$checklist_pergunta_id];

                    //     if ($itemPda) {
                    //         $helperTextPda = 'Ação planejada anteriormente: ' . $itemPda->descricao . '<span class="text-primary"><br>Status: ' . PlanoAcaoItemStatusEnum::toSelectArray()[$itemPda->status] . '</span>';
                    //     } else {
                    //         $helperTextPda = 'Não tem.';
                    //     }
                    // }

                    $tipo_pergunta = $v->tipo_pergunta;

                    $labelPergunta = $v->pergunta . ($v->pivot->fl_obrigatorio ? '*' : '');

                    $textoApoio = $v['texto_apoio'];// . ($v['texto_apoio'] ? '<br>' . $helperTextPda : $helperTextPda);

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
                        $upload_max_filesize = AppHelper::return_bytes(ini_get('upload_max_filesize'));
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

        /**
         * Bloco das Coordenadas (lat/lng)
         *
         * Via javascript é add o mapa
         */
        $this->add('card-coordenadas', 'card-start', [
            'id' => 'card-coordenadas',
            'title' => 'Coordenadas'
        ])->add('lat', 'text', [
            'label' => 'Latitude',
            'error' => __('validation.required', ['attribute' => 'Latitude']),
        ])->add('lng', 'text', [
            'label' => 'Longitude',
            'error' => __('validation.required', ['attribute' => 'Longitude']),
        ])->add('card-coordenadas-end', 'card-end', []);

        /**
         * Bloco Histórico
         */
        $this->add('card-historico-start', 'card-start', [
            'title' => 'Histórico da unidade produtiva',       
        ])->add('historico', 'textarea', [
            'label' => 'Histórico da unidade produtiva',                        
        ])->add('card-historico-end', 'card-end', []);        

        /**
         * Bloco dos dados complementares
         */
        $this->add('card-comp-start', 'card-start', [
            'id' => 'card-dados-complementares',
            'title' => 'Dados Complementares',
        ])->add('fl_certificacoes', 'select',
        [
            'label' => 'Possui Certificação?',
            'choices' => CheckboxEnum::toSelectArray()
        ])->add('card-certificacoes-start', 'fieldset-start', [
            'id' => 'card-certificacoes',
            'title' => 'Certificações'
        ])->add('certificacoes', 'select', [
            'label' => 'Certificações',
            'choices' => CertificacaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('certificacoes_descricao', 'text', [
            'label' => 'Certificações - Descrição',
        ])->add('card-certificacoes-end', 'fieldset-end', [])->add('fl_car', 'select', [
            'label' => 'Possui CAR?',
            'choices' => UnidadeProdutivaCarEnum::toSelectArray(),
        ])->add('car', 'number', [
            'label' => 'CAR',
            'wrapper' => [
                'id' => 'card-car'
            ],
        ])->add(
            'fl_ccir',
            'select',
            [
                'label' => 'Possui CCIR?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add(
            'fl_itr',
            'select',
            [
                'label' => 'Possui ITR?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add(
            'fl_matricula',
            'select',
            [
                'label' => 'Possui Matricula?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('upa', 'number', [
            'label' => 'Número da UPA',
            'help_block' => [
                'text' => 'Informação de Cadastro Estadual. Não preencher.'
            ],
        ])
        ->add('card-comp-end', 'card-end', []);

        /**
         * Bloco Uso do Solo - dados gerais
         */
        $this->add('card-solo-start', 'card-start', [
            'title' => 'Uso do Solo',
        ])->add('area_total_solo', 'number', [
            'label' => 'Área total da propriedade (' . config('app.area_sigla') . ')',
        ])->add('area_produtiva', 'number', [
            'label' => 'Área produtiva (' . config('app.area_sigla') . ')',
        ])->add('observacoes_sobre_area', 'text', [
            'label' => 'Observações sobre a área',                        
        ])->add('card-solo-end', 'card-end', []);

        /**
         * Bloco Uso do Solo - Iframe (adicionado via JS)
         */

        /**
         * Bloco Uso do Solo - dados gerais
         */
        $this->add('card-solo-outros-start', 'card-start', [
            'title' => 'Uso do Solo',
            'id' => 'card-outros-usos',
        ])->add('solosCategoria', 'select', [
            'label' => 'Outros Usos',
            'choices' => SoloCategoriaModel::where('tipo', 'outros')->pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('outros_usos_descricao', 'textarea', [
            'label' => 'Outros Usos - Descrição',
            'attr' => [
                'rows' => 2
            ],
            'wrapper' => [
                'id' => 'card-outros-usos',
            ]
        ])->add('card-solo-outros-end', 'card-end', []);

        /**
         * Bloco - Processa Produção
         */
        $this->add('card-processa-start', 'card-start', [            
            'title' => 'Processa Produção',
            'id' => 'card-processamento',
        ])->add('fl_producao_processa', 'select', [
            'choices' => ProcessaProducaoEnum::toSelectArray(),
            'empty_value' => 'Selecione',
            'label' => 'Processa a produção?',
        ])->add('producao_processa_descricao', 'textarea', [
            'label' => 'Descreva o processamento da produção',
            'attr' => [
                'rows' => 2,
            ],
            'wrapper' => [
                'id' => 'card-producao-processa',
            ]
        ])->add('card-processa-end', 'card-end', []);
        

        /**
         * Bloco - Comercialização
         */
        $this->add('card-comercializacao-start', 'card-start', [
            'title' => 'Comercialização',
            'id' => 'card-realiza-comercializacao',
        ])->add(
            'fl_comercializacao',
            'select',
            [
                'label' => 'Comercializa a Produção?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('canaisComercializacao', 'select', [
            'label' => 'Canais de Comercialização',
            'choices' => CanalComercializacaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
            'wrapper' => [
                'id' => 'card-comercializacao'
            ],
        ])->add('gargalos', 'text', [
            'label' => 'Gargalos',
            'attr' => [
                'placeholder' => 'Gargalos da produção, processamento e comercialização'
            ]
        ])->add('card-comercializacao-end', 'card-end', []);


        /**
         * Bloco - Saneamento Rural
         */
        $this->add('card-agua-start', 'card-start', [
            'title' => 'Saneamento Rural',
            'id' => 'card-saneamento',
        ])->add('outorga_id', 'select', [
            'label' => 'Possui Outorga?',
            'empty_value' => 'Selecione',
            'choices' => OutorgaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
        ])->add('tiposFonteAgua', 'select', [
            'label' => 'Fontes de uso de Água',
            // 'empty_value' => 'Selecione',
            'choices' => TipoFonteAguaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('agua_qualidade', 'text', [
            'label' => 'Qualidade da água',
            'attr' => [
                'placeholder' => 'Observações sobre a qualidade da água'
            ]            
        ])->add('agua_disponibilidade', 'text', [
            'label' => 'Disponibilidade de água',
            'attr' => [
                'placeholder' => 'Observações sobre a disponibilidade de água'
            ]                
        ])->add('fl_risco_contaminacao', 'select',
        [
            'label' => 'Há Risco de Contaminação?',
            'choices' => CheckboxEnum::toSelectArray()
        ])->add('riscosContaminacaoAgua', 'select', [
            'label' => 'Selecione os Tipos de Contaminação',
            'choices' => RiscoContaminacaoAguaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
            'wrapper' => [
                'class' => 'form-group row card-risco-contaminacao',
            ]
        ])->add('risco_contaminacao_observacoes', 'textarea', [
            'label' => 'Observações quanto à contaminação',
            'attr' => [
                'rows' => 2
            ],
            'wrapper' => [
                'class' => 'form-group row card-risco-contaminacao',
            ]
        ])->add('residuoSolidos', 'select', [
            'label' => 'Destinação de resíduos sólidos',
            'choices' => ResiduoSolidoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('esgotamentoSanitarios', 'select', [
            'label' => 'Esgotamento Sanitário',
            'choices' => EsgotamentoSanitarioModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('card-agua-end', 'card-end', []);

        /**
         * Bloco - N Pessoas (adicionado iframe via JS)
         */

        /**
         * Bloco - N Infra-Estrutura (adicionado iframe via JS)
         */

        /**
         * Bloco - Pressões Sociais
         */
        $this->add('card-pressoes-sociais-start', 'card-start', [
            'title' => 'Pressões Sociais',
            'id' => 'card-sente-pressoes-sociais',
        ])->add(
            'fl_pressao_social',
            'select',
            [
                'label' => 'Sente pressões sociais e urbanas?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('card-pressao-social-start', 'fieldset-start', [
            'id' => 'card-pressao-social',
            'title' => 'Pressão Social'
        ])->add('pressaoSociais', 'select', [
            'label' => 'Pressões Sociais',
            // 'empty_value' => 'Selecione',
            'choices' => PressaoSocialModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'attr' => [
                'multiple' => 'multiple',
            ],
        ])->add('pressao_social_descricao', 'textarea', [
            'label' => 'Pressão Social - Descrição',
            'attr' => [
                'rows' => 2
            ],
        ])->add('card-pressao-social-end', 'fieldset-end', [])
            ->add('card-pressoes-sociais-end', 'card-end', []);
        

        /**
         * Bloco Croqui - Anexo
         */
        
        $upload_max_filesize = AppHelper::return_bytes(ini_get('upload_max_filesize'));

        $this->add('card-croqui-start', 'card-start', [
            'title' => 'Croqui da Propriedade',
        ])->add('croqui_propriedade', 'file', [
            'label' => 'Arquivo',
            'rules' => 'max:' . $upload_max_filesize . '|mimes:doc,docx,pdf,xls,xlsx,png,jpg,jpeg,gif,txt',
            "maxlength" => $upload_max_filesize,
            'help_block' => [
                'text' => 'Tamanho máximo do arquivo: ' . ini_get('upload_max_filesize'),
            ]
        ])->add('card-croqui-end', 'card-end', []);

        /**
         * Bloco - N Arquivos (adicionado iframe via JS)
         */         

        $this->add('custom-redirect', 'hidden');

        $this->add('checklist_id', 'hidden')
            ->add('produtor_id', 'hidden')
            ->add('unidade_produtiva_id', 'hidden');
    }
}
