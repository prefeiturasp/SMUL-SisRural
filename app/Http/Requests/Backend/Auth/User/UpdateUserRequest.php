<?php

namespace App\Http\Requests\Backend\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest.
 */
class UpdateUserRequest extends FormRequest
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
            'document' => ['required'],
            'email' => ['required', 'email'],
            'first_name' => ['required'],
            'last_name' => ['required'],
        ];
    }
}
