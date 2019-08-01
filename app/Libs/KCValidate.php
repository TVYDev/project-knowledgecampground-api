<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 31-Jul-19
 * Time: 9:13 PM
 */

namespace App\Libs;

use Illuminate\Support\Facades\Validator;

class KCValidate
{
    use JsonResponse;

    const VALIDATION_USER_CHANGE_PASSWORD = 'user_change_password';
    const VALIDATION_USER_LOGIN = 'user_login';
    const VALIDATION_USER_REGISTER = 'user_register';

    private $validationRules = [
        self::VALIDATION_USER_CHANGE_PASSWORD => [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password|confirmed'
        ],
        self::VALIDATION_USER_LOGIN => [
            'email'     => 'required|email',
            'password'  => 'required'
        ],
        self::VALIDATION_USER_REGISTER => [
            'name'      => 'required|string|max:50',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8'
        ]
    ];

    public function doValidate($request, $ruleName) {
        $validator = Validator::make($request, $this->validationRules[$ruleName]);

        if($validator->fails()) {
            return $this->standardJsonValidationErrorResponse($validator->errors()->first());
        }

        return true;
    }
}
