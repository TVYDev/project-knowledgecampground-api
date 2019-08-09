<?php

namespace Tests\Unit;

use App\SystemMessage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemMessageTest extends TestCase
{
    /** @test */
    public function message_sys_en_kh_are_even_in_an_array()
    {
        $sysMsg = new SystemMessage();
        $exampleCodeMsg = 'KC_MSG_INVALID__NAME_REQUIRED';
        $arrayMessages = $sysMsg->getArrayMessageSysEnKh($exampleCodeMsg);

        $this->assertIsArray($arrayMessages);
        $this->assertCount(3, $arrayMessages);
        $this->assertArrayHasKey('sys', $arrayMessages);
        $this->assertArrayHasKey('en', $arrayMessages);
        $this->assertArrayHasKey('kh', $arrayMessages);
    }

    /** @test */
    public function messages_sys_are_given_correct()
    {
        $sysMsg = new SystemMessage();
        $exampleCodeMsg = 'KC_MSG_INVALID__NAME_REQUIRED';
        $arrayMessages = $sysMsg->getArrayMessageSysEnKh($exampleCodeMsg);

        $this->assertEquals('Name is required', $arrayMessages['sys'], 'Maybe wrong data or lose DB connection');
    }

    /** @test */
    public function values_of_messages_sys_en_kh_must_be_null_if_no_value_or_message_code_not_found()
    {
        $sysMsg = new SystemMessage();
        $arrayMessages = $sysMsg->getArrayMessageSysEnKh('stupid_code');

        $this->assertNull($arrayMessages['sys']);
        $this->assertNull($arrayMessages['en']);
        $this->assertNull($arrayMessages['kh']);
    }
}
