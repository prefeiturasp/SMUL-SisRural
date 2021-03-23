<?php

namespace App\Http\Requests\Backend\Auth\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Auth\PasswordRulesHelper;

/**
 * Class StoreUserRequest.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();

        $inputs['document'] = preg_replace('/[^0-9]/', '', @$inputs['document']);;

        $this->replace($inputs);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users')],
            'document' => ['required', Rule::unique('users')],
            'password' => PasswordRulesHelper::register($this->email),
            'roles' => ['required', 'array'],
        ];
    }

    public function attributes()
    {
        return [
            'document' => 'CPF',
            'email' => 'E-mail',
            'roles' => 'Habilidades',
        ];
    }
}
