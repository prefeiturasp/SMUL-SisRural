<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\CheckboxEnum;
use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do Produtor
 */
class ProdutorForm extends Form
{
    public function buildForm()
    {

        /**
         * Dados básicos do cadastro
         */
        $this->add('card-start', 'card-start', [
            'title' => 'Dados Básicos',
            'titleTag' => 'h1'
        ])->add('nome', 'text', [
            'label' => 'Nome Completo',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ])->add('cpf', 'text', [
            'label' => 'CPF (Cadastro de Pessoa Física)',
            'attr' => [
                'class' => 'form-control req-cpf',
                '_mask' => '999.999.999-99',
            ],
            'error' => 'CPF inválido'
        ])->add('telefone_1', 'text', [
            'label' => 'Telefone 1',
            'attr' => [
                '_mask' => '99 99999999?9',
            ],
        ])->add('telefone_2', 'text', [
            'label' => 'Telefone 2',
            'attr' => [
                '_mask' => '99 99999999?9',
            ],
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
        ])->add('tags', 'text', [
            'label' => 'Palavras-chave',
            'attr' => [
                'data-role' => 'tagsinput'
            ],
            'help_block' => [
                'text' => 'Insira as palavras, separando por vírgulas'
            ],
        ])->add('card-end', 'card-end', []);

        /**
         * Dados complementares do produtor
         */
        $this->add('card-dados-start', 'card-start', [
            'title' => 'Dados Complementares',
            'titleTag' => 'h2'
        ])->add('nome_social', 'text', [
            'label' => 'Nome Social',
            'error' => __('validation.required', ['attribute' => 'Nome Social'])
        ])->add('email', 'email', [
            'label' => 'E-mail',
        ])->add(
            'genero_id',
            'select',
            [
                'label' => 'Gênero ',
                'empty_value' => 'Selecione',
                'choices' => \App\Models\Core\GeneroModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'error' => __('validation.required', ['attribute' => 'Gênero'])
            ]
        )->add(
            'etinia_id',
            'select',
            [
                'label' => 'Cor, Raça e Etnia',
                'choices' => \App\Models\Core\EtiniaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                'empty_value' => 'Selecione',
                'error' => __('validation.required', ['attribute' => 'Cor'])
            ]
        );

        /**
         * Bloco - Portador de deficiencia
         */
        $this->add(
            'fl_portador_deficiencia', 'select',
            [
                'label' => 'Portador de Necessidades Especiais?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('card-portador-deficiencia-start', 'fieldset-start', [
            'id' => 'card-portador-deficiencia',
            'title' => 'Portador de Necessidades Especiais'
        ])->add(
            'portador_deficiencia_obs',
            'text',
            [
                'label' => 'Tipo de Necessidade Especial',
            ]
        )->add('card-portador-deficiencia-end', 'fieldset-end', [])->add('data_nascimento', 'date', [
            'label' => 'Data de Nascimento',
            'error' => __('validation.date', ['attribute' => 'Data de Nascimento'])
        ]);

        /**
         * Continuação Dados complementares
         */
        $this->add('rg', 'number', [
            'label' => 'RG (Registro Geral)',
        ])->add(
            'fl_cnpj',
            'select',
            [
                'label' => 'Possui CNPJ?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('cnpj', 'text', [
            'label' => 'CNPJ',
            'attr' => [
                'class' => 'form-control req-cnpj',
                '_mask' => '99.999.999/9999-99',
            ],
            'error' => 'CNPJ inválido',
            'wrapper' => [
                'id' => 'card-cnpj'
            ],
        ])->add(
            'fl_nota_fiscal_produtor',
            'select',
            [
                'label' => 'Possui Nota Fiscal de Produtor/a?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('nota_fiscal_produtor', 'text', [
            'label' => 'Número Nota Fiscal de Produtor/a',
            'wrapper' => [
                'id' => 'card-nota-fiscal-produtor'
            ],
        ])->add(
            'fl_agricultor_familiar',
            'select',
            [
                'label' => 'É Agricultor/a Familiar?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        );

        /**
         * Bloco Agricultor Familiar
         */
        $this->add('card-agricultor-familiar-start', 'fieldset-start', [
            'id' => 'card-agricultor-familiar',
            'title' => 'Agricultor Familiar'
        ])->add(
            'fl_agricultor_familiar_dap',
            'select',
            [
                'label' => 'Possui DAP (Declaração de Aptidão ao Pronaf)?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('agricultor_familiar_numero', 'text', [
            'label' => 'Número DAP',
            'wrapper' => [
                'class' => 'form-group row card-agricultor-familiar-dap',                
            ],
        ])->add('agricultor_familiar_data', 'date', [
            'label' => 'Validade DAP',
            'error' => __('validation.required', ['attribute' => 'Validade DAP']),
            'wrapper' => [
                'class' => 'form-group row card-agricultor-familiar-dap'
            ],
        ])->add('card-agricultor-familiar-end', 'fieldset-end', []);

        /**
         * Bloco Assistencia Técnica
         */
        $this->add(
            'fl_assistencia_tecnica',
            'select',
            [
                'label' => 'Recebe Assistência Técnica?',
                'choices' => CheckboxEnum::toSelectArray(),
                'wrapper' => [
                    'id' => 'bloco-assistencia-tecnica'
                ],
            ]
        )->add('card-assistencia-tecnica-start', 'fieldset-start', [
                'id' => 'card-assistencia-tecnica',
                'title' => 'Assistência Técnica'
            ])->add(
                'assistencia_tecnica_tipo_id',
                'select',
                [
                    'label' => 'Qual o Tipo da Assistência Técnica',
                    'empty_value' => 'Selecione',
                    'choices' => \App\Models\Core\AssistenciaTecnicaTipoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
                ]
            )->add(
                'assistencia_tecnica_periodo',
                'text',
                [
                    'label' => 'Periodicidade da Assistência Técnica',
                ]
        )->add('card-assistencia-tecnica-end', 'fieldset-end', []);

        /**
         * Bloco Contratação de Mão de Obra Externa
         */
        $this->add(
            'fl_contrata_mao_de_obra_externa',
            'select',
            [
                'label' => 'Contrata mão-de-obra externa?',
                'choices' => CheckboxEnum::toSelectArray(),
                'wrapper' => [
                    'id' => 'bloco-mao-de-obra-externa'
                ],
            ]
        )->add('card-mao-de-obra-externa-start', 'fieldset-start', [
                'id' => 'card-mao-de-obra-externa',
                'title' => 'Mão-de-obra externa'
            ])->add(
                'mao_de_obra_externa_tipo',
                'text',
                [
                    'label' => 'Para qual o tipo de trabalho contrata mão-de-obra externa?',
                ]
            )->add(
                'mao_de_obra_externa_periodicidade',
                'text',
                [
                    'label' => 'Periodicidade da contratação de mão-de-obra externa',
                ]
        )->add('card-mao-de-obra-externa-end', 'fieldset-end', []);        

        /**
         * Bloco Comunidade Tradicional
         */
        $this->add(
            'fl_comunidade_tradicional',
            'select',
            [
                'label' => 'É de Comunidade Tradicional?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        );         
        $this->add('card-comunidade-tradicional-start', 'fieldset-start', [
            'id' => 'card-comunidade-tradicional',
            'title' => 'Comunidade Tradicional'
        ])->add(
            'comunidade_tradicional_obs',
            'text',
            [
                'label' => 'Qual Comunidade Tradicional?',
            ]
        )->add('card-comunidade-tradicional', 'fieldset-end', [])->add(
            'fl_internet',
            'select',
            [
                'label' => 'Acessa a Internet?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        );


        /**
         * Continuação bloco dados complementares
         */
        $this->add(
            'fl_tipo_parceria',
            'select',
            [
                'label' => 'Participa de Cooperativa, Associação, Rede, Movimento ou Coletivo?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add(
            'tipo_parcerias_obs',
            'textarea',
            [
                'label' => 'Qual?',
                'attr' => [
                    'rows' => 2
                ],
                'wrapper' => [
                    'id' => 'card-tipo-parceria'
                ],
            ]
        )->add(
            'renda_agricultura_id',
            'select',
            [
                'label' => '% da renda advinda da agricultura',
                'empty_value' => 'Selecione',
                'choices' => \App\Models\Core\RendaAgriculturaModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            ]
        )->add(
            'rendimento_comercializacao_id',
            'select',
            [
                'label' => 'Rendimento da comercialização',
                'empty_value' => 'Selecione',
                'choices' => \App\Models\Core\RendimentoComercializacaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            ]
        )->add('outras_fontes_renda', 'textarea', [
            'label' => 'Outras fontes de renda',
            'error' => __('validation.required', ['attribute' => 'Outras fontes de renda']),
            'attr' => [
                'rows' => 2
            ],
        ])->add(
            'grau_instrucao_id',
            'select',
            [
                'label' => 'Grau de instrução',
                'empty_value' => 'Selecione',
                'choices' => \App\Models\Core\GrauInstrucaoModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            ]
        )->add(
            'situacao_social_id',
            'select',
            [
                'label' => 'Situação Social',
                'empty_value' => 'Selecione',
                'choices' => \App\Models\Core\SituacaoSocialModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            ]            
        )->add('fl_reside_unidade_produtiva',
            'select',
            [
                'label' => 'Reside na Unidade Produtiva?',
                'choices' => CheckboxEnum::toSelectArray()
            ]
        )->add('card-endereco-start', 'fieldset-start', [
            'id' => 'card-endereco',
            'title' => 'Endereço'
        ])->add('cep', 'text', [
            'label' => 'CEP (Código de Endereçamento Postal)',
            'attr' => [
                '_mask' => '99999-999',
            ],
        ])->add('endereco', 'text', [
            'label' => 'Endereço',
        ])->add('bairro', 'text', [
            'label' => 'Bairro',
        ])->add('subprefeitura', 'text', [
            'label' => 'Distrito',            
        ])->add(
                'cidade_id',
                'select',
                [
                    'label' => 'Município',
                    'empty_value' => 'Selecione',
                    'choices' => @$this->model->estado_id ? \App\Models\Core\CidadeModel::where('estado_id', @$this->model->estado_id)->pluck('nome', 'id')->sortBy('nome')->toArray() : [],
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Município'])
        ])->add(
                'estado_id',
                'select',
                [
                    'label' => 'Estado',
                    'empty_value' => 'Selecione',
                    'choices' => \App\Models\Core\EstadoModel::orderByRaw('FIELD(uf, "SP") DESC, nome')->pluck('nome', 'id')->toArray(),
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Estado'])
        ])->add('card-dados-end', 'card-end', [

        ])->add('card-endereco-end', 'fieldset-end', []);

        $this->add('custom-redirect', 'hidden');
    }
}
