<?php

namespace Tests\Unit;

use App\ThirdPartyApiUrl;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThirdPartyApiUrlTest extends TestCase
{
    /** @test */
    public function returned_url_is_string()
    {
        $exampleKey = 'jdenticon';
        $thirdParty = new ThirdPartyApiUrl();
        $url = $thirdParty->getApiUrl($exampleKey);

        $this->assertIsString($url);
    }

    /** @test */
    public function returned_url_is_prefixed_with_http_or_https()
    {
        $exampleKey = 'jdenticon';
        $thirdParty = new ThirdPartyApiUrl();
        $url = $thirdParty->getApiUrl($exampleKey);

        $this->assertRegExp('/^(https|http):\/\/[\w\W]+$/', $url);
    }
}
