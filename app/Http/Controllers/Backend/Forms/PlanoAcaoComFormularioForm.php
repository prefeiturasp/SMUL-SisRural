<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\PlanoAcaoStatusEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário utilizado pelo PDA com Formulário
 */
class PlanoAcaoComFormularioForm extends Form
{
    public function buildForm()
    {
        $produtor = @$this->data['produtor'];
        $unidadeProdutiva = @$this->data['unidadeProdutiva'];
        $checklist = @$this->data['checklist'];

        //Remove os status que não podem ser utilizados (o sistema controla esses status)
        $planoAcaoStatusEnum = PlanoAcaoStatusEnum::toSelectArray();
        unset($planoAcaoStatusEnum['aguardando_aprovacao']);
        unset($planoAcaoStatusEnum['em_andamento']);
        unset($planoAcaoStatusEnum['concluido']);
        unset($planoAcaoStatusEnum['cancelado']);

        //Retira o "Não iniciado" da listagem do STATUS quando for um "create"
        if (@!$this->model->id) {
            unset($planoAcaoStatusEnum['nao_iniciado']);
        }

        //Altera o nome do "não iniciado" quando for um formulário que ainda esta na fase de detalhamento
        if (@$planoAcaoStatusEnum['nao_iniciado']) {
            $planoAcaoStatusEnum['nao_iniciado'] = 'Prosseguir';
        }

        /**
         * Bloco de informações estaticas
         */
        if (@$produtor && @$unidadeProdutiva && @$checklist) {
            $this->add('card-start', 'card-start', [
                'title' => 'Informações gerais',
                'titleTag' => 'h1'
            ])->add('checklist', 'static', [
                'label' => 'Formulário',
                'tag' => 'b',
                'value' => $checklist['nome']
            ])->add('produtor', 'static', [
                'label' => 'Produtor/a',
                'tag' => 'b',
                'value' => $produtor['nome']
            ]);

            if (@$unidadeProdutiva['socios']) {
                $this->add('socios', 'static', [
                    'label' => 'Coproprietários',
                    'tag' => 'b',
                    'value' => $unidadeProdutiva['socios']
                ]);
            }

            $this->add('unidadeProdutiva', 'static', [
                'label' => 'Unidade Produtiva',
                'tag' => 'b',
                'value' => $unidadeProdutiva['nome']
            ])->add('instrucoes', 'static', [
                'label' => 'Instruções Gerais',
                'tag' => 'span',
                'value' => @$checklist['instrucoes_pda'] ?  $checklist['instrucoes_pda'] : 'Não há'
            ])->add('card-end', 'card-end', []);
        }

        /**
         * Bloco de informações do PDA
         */
        $this->add('card-inf-pda-start', 'card-start', [
            'title' => 'Informações do plano de ação',
            'titleTag' => 'h1'
        ])->add('nome', 'text', [
            'label' => 'Título',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Título']),
        ])->add('prazo', 'date', [
            'label' => 'Prazo',
            'help_block' => [
                'text' => 'Formato da data: Dia/Mês/Ano. Ex: 31/12/2000',
                'tag' => 'b'
            ],
        ])->add('card-inf-pda-end', 'card-end', []);

        /**
         * Bloco do Status
         */
        $this->add('card-status-start', 'card-start', [
            'title' => 'Etapa do Plano de Ação',
            'titleTag' => 'h1',
            'help_block' => [
                'text' => 'Mantenha em rascunho para detalhar as ações. Use "Prosseguir" para iniciar o acompanhamento do Plano de Ação ou enviar para aprovação, se for o caso.',
                'tag' => 'b'
            ],
        ])->add(
            'status',
            'select',
            [
                'label' => 'Status',
                'choices' => $planoAcaoStatusEnum,
                'empty_value' => 'Selecione',
                'default_value' => 'rascunho',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status'])
            ]
        )->add('card-status-end', 'card-end', []);

        $this->add('produtor_id', 'hidden')
            ->add('unidade_produtiva_id', 'hidden')
            ->add('checklist_unidade_produtiva_id', 'hidden');
    }
}
