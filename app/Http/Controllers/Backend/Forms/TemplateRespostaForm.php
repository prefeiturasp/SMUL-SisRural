<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Respostas das perguntas (caderno de campo) que possuem multipla escolha ou unica escolha (semafórica, binária ...)
 */
class TemplateRespostaForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        $this->add('descricao', 'textarea', [
            'label' => 'Resposta',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Resposta']),
            'attr' => [
                'rows' => 2
            ],
        ]);

        $this->add('card-end', 'card-end', []);
    }
}
