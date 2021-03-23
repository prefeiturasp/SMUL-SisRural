<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário - Roles do Usuário
 */
class UserRolesForm extends Form
{
    public function buildForm()
    {
        $readonly = @$this->model ? 'readonly' : null;

        $this->add(
            'role',
            'select',
            [
                'label' => 'Papéis',
                'choices' => @$this->data['roles'],
                'rules' => 'required',
                'empty_value' => 'Selecione',
                'attr' => [
                    'readonly' => $readonly
                ]
            ]
        )->add(
            'dominio',
            'select',
            [
                'label' => 'Domínio',
                'choices' => @$this->data['dominios'],
                // 'rules' => 'required', //Adicionado via JS, caso seja "Administrator" não é "required"
                'empty_value' => 'Selecione',
                'attr' => [
                    'readonly' => $readonly
                ]
            ]
        )->add(
            'unidades_operacionais',
            'select',
            [
                'label' => 'Unidades Operacionais',
                // 'rules' => 'required', //Não é required, backend trata porque o Usuario A faz parte de B e C, mas o usuário logado só tem permissão de ver B.
                'choices' =>  @$this->data['unidades_operacionais'],
                'attr' => [
                    'multiple' => 'multiple',
                ],
            ]
        );
    }
}
