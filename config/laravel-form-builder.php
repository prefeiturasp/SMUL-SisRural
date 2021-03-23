<?php

//namespace Kris\LaravelFormBuilder\Fields\
//private function allDefaults()

return [
    'defaults'      => [
        'wrapper_class'       =>  'form-group row',
        'wrapper_error_class' =>  'has-error',
        'label_class'         =>  'form-control-label col-md-10',  //col-md-10 //col-md-2 //'control-label',
        'field_class'         =>  'form-control',
        'field_error_class'   =>  '',
        'help_block_class'    =>  'form-text text-muted small',
        'error_class'         =>  'text-danger',
        'required_class'      =>  'required',

        'file' => [
            'wrapper_class' => 'custom-file form-group row',
            'field_class' => ' custom-file-input',
            'label_class' => 'custom-file-label col-md-6', //col-md-6
        ],

        'choice' => [
            'choice_options' => [
                'wrapper_class' => 'form-check',
                'label_class' => 'form-check-label',
                'field_class' => 'form-check-input',
            ],
        ],
    ],
    // Templates
    'form'          => 'laravel-form-builder::form',
    'text'          => 'laravel-form-builder::text',
    'textarea'      => 'laravel-form-builder::textarea',
    'button'        => 'laravel-form-builder::button',
    'buttongroup'   => 'laravel-form-builder::buttongroup',
    'radio'         => 'laravel-form-builder::radio',
    'checkbox'      => 'laravel-form-builder::checkbox',
    'select'        => 'laravel-form-builder::select',
    'choice'        => 'laravel-form-builder::choice',
    'repeated'      => 'laravel-form-builder::repeated',
    'child_form'    => 'laravel-form-builder::child_form',
    'collection'    => 'laravel-form-builder::collection',
    'static'        => 'laravel-form-builder::static',

    'template_prefix'   => '',

    'default_namespace' => '',

    'custom_fields' => [
        'title' => App\Forms\Fields\TitleType::class,
        'fieldset-start' => App\Forms\Fields\FieldsetStartType::class,
        'fieldset-end' => App\Forms\Fields\FieldsetEndType::class,
        'card-start' => App\Forms\Fields\CardStartType::class,
        'card-end' => App\Forms\Fields\CardEndType::class,
        'row-start' => App\Forms\Fields\RowStartType::class,
        'row-end' => App\Forms\Fields\RowEndType::class
    ]
];
