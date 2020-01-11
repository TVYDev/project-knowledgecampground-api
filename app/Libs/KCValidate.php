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
    const VALIDATION_QUESTION_SAVE = 'valid_question_save';
    const VALIDATION_QUESTION_SAVE_DURING_EDITING = 'valid_question_save_during_editing';
    const VALIDATION_ANSWER_SAVE = 'valid_answer_save';
    const VALIDATION_ANSWER_SAVE_DURING_EDITING = 'valid_answer_save_during_editing';
    const VALIDATION_COMMENT_SAVE = 'valid_comment_save';
    const VALIDATION_REPLY_SAVE = 'valid_reply_save';

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
        ],
        self::VALIDATION_QUESTION_SAVE_DURING_EDITING => [
            'title'             => 'string|max:250',
            'description'       => 'required|string',
            'public_id'         => 'required|string',
            'is_draft'          => 'required|boolean'
        ],
        self::VALIDATION_QUESTION_SAVE => [
            'title'         => 'required|string|max:250',
            'subject_public_id' => 'required|string|max:500',
            'is_draft'      => 'required|boolean'
        ],
        self::VALIDATION_ANSWER_SAVE_DURING_EDITING => [
            'description'       => 'required|string',
            'public_id'         => 'required|string',
            'is_draft'          => 'required|boolean',
            'question_public_id'=> 'required|string|max:500'
        ],
        self::VALIDATION_ANSWER_SAVE => [
            'is_draft' => 'required|boolean'
        ],
        self::VALIDATION_COMMENT_SAVE => [
            'commentable_public_id' => 'required|string',
            'commentable_type' => 'required|string',
            'body'  => 'required|string'
        ],
        self::VALIDATION_REPLY_SAVE => [
            'comment_public_id' => 'required|string',
            'body' => 'required|string'
        ]
    ];

    /**
     * Example result
     * Validation for user register
     *
     * array:8 [
        "name.required" => "KC_MSG_INVALID__NAME_REQUIRED"
        "name.string" => "KC_MSG_INVALID__NAME_STRING"
        "name.max" => "KC_MSG_INVALID__NAME_MAX_50"
        "email.required" => "KC_MSG_INVALID__EMAIL_REQUIRED"
        "email.email" => "KC_MSG_INVALID__EMAIL_EMAIL"
        "email.unique" => "KC_MSG_INVALID__EMAIL_UNIQUE_USERS_EMAIL"
        "password.required" => "KC_MSG_INVALID__PASSWORD_REQUIRED"
        "password.min" => "KC_MSG_INVALID__PASSWORD_MIN_8"
        ]
     */
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

    public function getAllKeyValidationRuleNames() {
        $names = [];
        foreach ($this->validationRules as $key => $value){
            array_push($names, $key);
        }
        return $names;
    }
}
