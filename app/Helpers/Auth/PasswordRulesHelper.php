<?php

namespace App\Helpers\Auth;

use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;

/**
 * Class PasswordRules.
 */
class PasswordRulesHelper extends PasswordRules
{
    /**
     * Regras para o password
     *
     * @param  mixed $username
     * @return array
     */
    public static function register($username)
    {
        return [
            'required',
            'string',
            'min:8'
        ];
    }

    public static function changePassword($username, $oldPassword = null)
    {
        $rules = self::register($username);

        if ($oldPassword) {
            $rules = array_merge($rules, [
                'different:' . $oldPassword,
            ]);
        }

        return $rules;
    }

    public static function optionallyChangePassword($username, $oldPassword = null)
    {
        $rules = self::changePassword($username, $oldPassword);

        $rules = array_merge($rules, [
            'nullable',
        ]);

        foreach ($rules as $key => $rule) {
            if (is_string($rule) && $rule === 'required') {
                unset($rules[$key]);
            }
        }

        return $rules;
    }
}
