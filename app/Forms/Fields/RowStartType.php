<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class RowStartType extends FormField {

    protected function getTemplate()
    {
        return 'vendor.laravel-form-builder.row-start';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options);
    }
}

?>
