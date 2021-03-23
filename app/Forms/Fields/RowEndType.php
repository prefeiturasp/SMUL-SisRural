<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class RowEndType extends FormField {

    protected function getTemplate()
    {
        return 'vendor.laravel-form-builder.row-end';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options);
    }
}

?>
