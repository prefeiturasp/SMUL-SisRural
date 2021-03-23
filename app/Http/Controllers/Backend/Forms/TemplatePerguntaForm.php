<?php

namespace App\Http\Controllers\Backend\Forms;

use App\Enums\TipoTemplatePerguntaEnum;
use Kris\LaravelFormBuilder\Form;

/**
 * Formulário das perguntas utilizadas no template do Caderno de Campo
 */
class TemplatePerguntaForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        $this->add(
            'tipo',
            'select',
            [
                'label' => 'Tipo',
                'choices' => TipoTemplatePerguntaEnum::toSelectArray(),
                'empty_value' => 'Selecione',
                'rules' => 'required',
                'error' => __('validation.required', ['attribute' => 'Tipo'])
            ]
        )->add('pergunta', 'textarea', [
            'label' => 'Pergunta',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Pergunta']),
            'attr' => [
                'rows' => 2
            ],
        ])->add('tags', 'text', [
            'label' => 'Palavras-chave',
            'error' => __('validation.required', ['attribute' => 'Palavras-chave']),
            'attr' => [
                'data-role' => 'tagsinput'
            ],
            'help_block' => [
                'text' => 'Insira as palavras, separando por vírgulas'
            ],
        ]);

        $this->add('card-end', 'card-end', []);

        $this->add('custom-redirect', 'hidden');
    }
}
