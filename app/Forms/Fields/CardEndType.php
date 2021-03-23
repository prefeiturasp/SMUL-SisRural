<?php

namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class CardEndType extends FormField
{

    /**
     * Componente custom p/ ser utilizado dentro do FormBuilder.
     *
     * Este elemento é um "Card" do Bootstrap
     *
     *   $this->add('card-start', 'card-start', [
     *       'title' => 'Informações principais',
     *   ])->add('card-end', 'card-end');
     */
    protected function getTemplate()
    {
        return 'vendor.laravel-form-builder.card-end';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options);
    }
}
