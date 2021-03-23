<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário Solo Categoria
 */
class SoloCategoriaForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        $this->add('nome', 'text', [
            'label' => 'Nome',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ])->add(
            'tipo',
            'select',
            [
                'label' => 'Tipo da Categoria',
                'choices' => [
                    'geral' => 'Uso do Solo',
                    'outros' => 'Outros Usos'
                ],
                'empty_value' => 'Selecione',
                // 'help_block' => [
                //     'text' => "Uso do Solo: Utilizado na sessão Uso do Solo, na unidade produtiva. <br>Outros Usos: Campo da unidade produtiva."
                // ],
            ]
        )->add(
            'tipo_form',
            'select',
            [
                'label' => 'Tipo do Formulário - Uso do Solo',
                'choices' => [
                    'todos' => 'Formulário padrão',
                    'hectares' => "Formulário com apenas o campo 'Área (Hectares)'"
                ],
                'empty_value' => 'Selecione',
                'help_block' => [
                    'text' => "Utilizado para categoria do tipo 'Uso do Solo'.<br>Distingue o formato do formulário. Ex: Pousio vs Apicultura."
                ],
            ]
        )->add('min', 'number', [
            'label' => 'Agrobiodiversidade - Baixo',
            'rules' => 'required|numeric',
            'error' => __('validation.required', ['attribute' => 'Agrobiodiversidade - Baixo'])
        ])->add('max', 'number', [
            'label' => 'Agrobiodiversidade - Alto',
            'rules' => 'required|numeric',
            'error' => __('validation.required', ['attribute' => 'Agrobiodiversidade - Alto'])
        ]);

        $this->add('card-end', 'card-end', []);
    }
}
