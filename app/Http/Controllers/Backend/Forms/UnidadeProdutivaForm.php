<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\CheckboxEnum;
use App\Enums\ProcessaProducaoEnum;
use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use App\Enums\UnidadeProdutivaCarEnum;
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
        } else if ($this->model && $this->model->produtores) {
            $this->add('card-start-pr', 'card-start', ['title' => 'Informações Gerais']);

            $this->add('produtor', 'static', [
                'label' => 'Produtores',
                'tag' => 'b',
                'value' => join(", ", $this->model->produtores->pluck('nome')->toArray())
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
        ])->add('cep', 'text', [
            'label' => 'CEP (Código de Endereçamento Postal)',
            'attr' => [
                '_mask' => '99999-999',
            ],
        ])->add('endereco', 'text', [
            'label' => 'Endereço',
            'rules' => 'required',
        ])->add('bairro', 'text', [
            'label' => 'Bairro',
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
                'choices' => @$this->model->estado_id ? \App\Models\Core\CidadeModel::where('estado_id', @$this->model->estado_id)->pluck('nome', 'id')->sortBy('nome')->toArray() : [],
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Município'])
            ]
        )->add('bacia_hidrografica', 'text', [
            'label' => 'Bacia Hidrográfica',
        ])->add(
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
        ])->add('card-dados-end', 'card-end');


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
         * Bloco dos dados complementares
         */
        $this->add('card-comp-start', 'card-start', [
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
        ])->add('area_total_solo', 'text', [
            'label' => 'Área total da propriedade',
        ])->add('card-solo-end', 'card-end', []);

        /**
         * Bloco Uso do Solo - Iframe (adicionado via JS)
         */

        /**
         * Bloco Uso do Solo - dados gerais
         */
        $this->add('card-solo-outros-start', 'card-start', [
            'title' => 'Uso do Solo',
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
        $this->add('card-croqui-start', 'card-start', [
            'title' => 'Croqui da Propriedade',
        ])->add('croqui_propriedade', 'file', [
            'label' => 'Arquivo',
            'rules' => 'max:25600|mimes:doc,docx,pdf,xls,xlsx,png,jpg,jpeg,gif,txt',
            "maxlength" => 25600,
            'help_block' => [
                'text' => 'Tamanho máximo do arquivo: 25mb',
            ]
        ])->add('card-croqui-end', 'card-end', []);

        /**
         * Bloco - N Arquivos (adicionado iframe via JS)
         */

        $this->add('custom-redirect', 'hidden');
    }
}
