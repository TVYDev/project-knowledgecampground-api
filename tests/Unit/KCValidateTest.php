<?php

namespace Tests\Unit;

use App\Libs\KCValidate;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class KCValidateTest extends TestCase
{
    /** @test */
    public function array_is_returned_when_generating_validation_message_codes_from_rules()
    {
        $kcValidator = new KCValidate();
        $result = $kcValidator->getGeneratedArrayValidationMessageCodesFromRule(KCValidate::VALIDATION_USER_LOGIN);

        $this->assertIsArray($result);
    }

    /** @test */
    public function generated_validation_message_codes_are_formatted_and_prefixed_correctly()
    {
        $kcValidator = new KCValidate();
        $result = $kcValidator->getGeneratedArrayValidationMessageCodesFromRule(KCValidate::VALIDATION_USER_REGISTER);

        $prefix = 'KC_MSG_INVALID__';

        foreach ($result as $r){
            $this->assertRegExp('/^[A-Z0-9_]+$/', $r);
            $this->assertContains($prefix, $r);
        }
    }

    /** @test */
    public function generated_validation_message_codes_has_keys_all_lower_cases_with_dot_separated()
    {
        $kcValidator = new KCValidate();
        $result = $kcValidator->getGeneratedArrayValidationMessageCodesFromRule(KCValidate::VALIDATION_USER_REGISTER);

        foreach ($result as $k => $r){
            $this->assertRegExp('/^[a-z]+\.[a-z]+$/', $k);
        }
    }
}
