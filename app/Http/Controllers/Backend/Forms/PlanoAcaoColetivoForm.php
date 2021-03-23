<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\PlanoAcaoStatusEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do PDA Coletivo
 */
class PlanoAcaoColetivoForm extends Form
{
    public function buildForm()
    {
        $isEdit = @$this->model && @$this->model->id;

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

        //Caso seja edição (registro já foi criado), permite alterar o status
        if ($isEdit) {

            //Retira os status que não podem mais ser utilizados (controlado pelo sistema)
            $planoAcaoStatusEnum = PlanoAcaoStatusEnum::toSelectArray();
            unset($planoAcaoStatusEnum['rascunho']); //Não existe "rascunho" no PDA Coletivo
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
    }
}
