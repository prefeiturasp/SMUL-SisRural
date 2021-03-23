<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário das Categorias de um Formulário
 *
 * Checklist (template formulário) -> tem N Categorias
 */
class ChecklistCategoriaForm extends Form
{
    public function buildForm()
    {
        $this->add('nome', 'text', [
            'label' => 'Nome da categoria',
            'rules' => 'required',
            'attr' => [
                'autocomplete' => 'off',
            ],
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ]);
    }
}
