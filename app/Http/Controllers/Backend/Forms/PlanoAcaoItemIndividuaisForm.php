<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\PlanoAcaoItemStatusEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário utilizado na criação do item do PDA Individual
 *
 * O primeiro cadastro só tem o status, logo após salvar o usuário já é redirecionado para a "EDIÇÃO", liberando o "PlanoAcahoItemForm.php"
 */
class PlanoAcaoItemIndividuaisForm extends Form
{
    public function buildForm()
    {
        $this->add(
            'status',
            'select',
            [
                'label' => 'Status da ação',
                'choices' => PlanoAcaoItemStatusEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Status']),
            ]
        );
    }
}
