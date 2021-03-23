<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do template do caderno de campo
 */
class TemplateCadernoForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        /**
         * dominio_id é o "dono" do caderno de campo
         */
        $this->add('dominio_id', 'select', [
            'label' => 'Domínio (Dono)',
            'choices' => \App\Models\Core\DominioModel::pluck('nome', 'id')->sortBy('nome')->toArray(),
            'empty_value' => 'Selecione',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Domínio'])
        ]);

        $this->add('nome', 'text', [
            'label' => 'Nome',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome']),
        ]);

        $this->add('card-end', 'card-end', []);

        $this->add('custom-redirect', 'hidden');
    }
}
