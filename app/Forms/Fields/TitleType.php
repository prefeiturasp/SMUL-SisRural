<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class TitleType extends FormField {

    protected function getTemplate()
    {
        return 'vendor.laravel-form-builder.title';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options);
    }
}
