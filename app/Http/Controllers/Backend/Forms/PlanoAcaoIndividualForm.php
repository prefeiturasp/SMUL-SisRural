<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\PlanoAcaoStatusEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do Plano de Ação Individual / Formulário
 */
class PlanoAcaoIndividualForm extends Form
{
    public function buildForm()
    {
        $isEdit = @$this->model && @$this->model->id;

        $produtor = @$this->data['produtor'];
        $unidadeProdutiva = @$this->data['unidadeProdutiva'];
        $checklistUnidadeProdutiva = @$this->data['checklistUnidadeProdutiva']; //Só vai ter esse campo se for um PDA de Formulário

        /**
         * Bloco de informações, são conteúdos "estaticos"
         */
        if (@$produtor && @$unidadeProdutiva) {
            $checklist = @$checklistUnidadeProdutiva->checklist;

            $this->add('card-start', 'card-start', [
                'title' => 'Informações gerais',
                'titleTag' => 'h1'
            ]);

            // Se possuí um formulário atrelado, retorna o nome do template do formulário
            if ($checklistUnidadeProdutiva && $checklistUnidadeProdutiva->id) {
                $this->add('checklist', 'static', [
                    'label' => 'Formulário',
                    'tag' => 'b',
                    'value' =>  $checklist['nome']
                ]);
            }

            $this->add('produtor', 'static', [
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
         * Informações do plano de Ação
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
        ]);

        //Se é edição, libera o bloco "status"
        if ($isEdit) {
            $planoAcaoStatusEnum = PlanoAcaoStatusEnum::toSelectArray();
            unset($planoAcaoStatusEnum['rascunho']);
            unset($planoAcaoStatusEnum['aguardando_aprovacao']);

            $this->add(
                'status',
                'select',
                [
                    'label' => 'Status',
                    'choices' => $planoAcaoStatusEnum,
                    'empty_value' => 'Selecione',
                    'rules' => 'required',
                    'error' => __('validation.required', ['attribute' => 'Status'])
                ]
            );
        }

        $this->add('card-inf-pda-end', 'card-end', []);

        $this->add('produtor_id', 'hidden')
            ->add('unidade_produtiva_id', 'hidden');
    }
}
