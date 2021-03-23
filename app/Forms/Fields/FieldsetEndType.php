<?php

namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class FieldsetEndType extends FormField
{

    /**
     * Componente custom p/ ser utilizado dentro do FormBuilder.
     *
     * Este elemento é um "Card" do Bootstrap
     *
     * $this->add('fieldset-start', 'fieldset-end')->add('fieldset-end', 'fieldset-end');
     */
    protected function getTemplate()
    {
        return 'vendor.laravel-form-builder.fieldset-end';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options);
    }
}
