<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class ProdutorRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $inputs = $this->all();

        if (@$inputs['cpf']) {
            $inputs['cpf'] = preg_replace('/[^0-9]/', '', $inputs['cpf']);;
        }

        if (@$inputs['cnpj']) {
            $inputs['cnpj'] = preg_replace('/[^0-9]/', '', $inputs['cnpj']);;
        }

        $this->replace($inputs);
    }

    public function rules()
    {
        return [];
    }
}
