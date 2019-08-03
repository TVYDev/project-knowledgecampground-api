<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 31-Jul-19
 * Time: 9:13 PM
 */

namespace App\Libs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class KCValidate
{
    use JsonResponse;

    private $prefixValidationMessageCode = 'KC_MSG_INVALID__';

    const VALIDATION_USER_CHANGE_PASSWORD = 'valid_user_change_password';
    const VALIDATION_USER_LOGIN = 'valid_user_login';
    const VALIDATION_USER_REGISTER = 'valid_user_register';

    private $validationRules = [
        self::VALIDATION_USER_CHANGE_PASSWORD => [
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password|confirmed'
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

    private function doGenerateArrayValidationMessageCodesFromRule($keyValidationRule)
    {
        $validationMessageCodes = [];
        foreach ($this->validationRules[$keyValidationRule] as $field=>$fieldRule) {
            $arrayFieldRule = explode('|', $fieldRule);
            foreach ($arrayFieldRule as $specificFieldRule) {
                $onlyFirstPartOfSpecificFieldRule = explode(':', $specificFieldRule)[0];
                $formattedSpecificFieldRule = strtoupper(preg_replace('/[:,]/', '_', $specificFieldRule));
                $validationMessageCodes[$field . '.' . $onlyFirstPartOfSpecificFieldRule] = $this->prefixValidationMessageCode . strtoupper($field) . '_' . $formattedSpecificFieldRule;
            }
        }
        return $validationMessageCodes;
    }

    public function getGeneratedArrayValidationMessageCodesFromRule($keyValidationRule)
    {
        $arrayMessageCodes = Cache::rememberForever($keyValidationRule, function () use ($keyValidationRule){
            return $this->doGenerateArrayValidationMessageCodesFromRule($keyValidationRule);
        });
        return $arrayMessageCodes;
    }

    public function doValidate($request, $ruleName) {
        $validator = Validator::make($request, $this->validationRules[$ruleName], $this->getGeneratedArrayValidationMessageCodesFromRule($ruleName));

        if($validator->fails()) {
            $messageCode = $validator->errors()->first();
            return $this->standardJsonValidationErrorResponse($messageCode);
        }

        return true;
    }
}
