<?php

namespace Tests\Unit;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JsonResponseTest extends TestCase
{
    use JsonResponse;

    /** @test */
    public function json_response_return_correct_json_format()
    {
        $json = $this->standardJsonResponse(HttpStatusCode::SUCCESS_OK, true, 'KC_MSG_ERROR__INVALID_TOKEN');

//        $this->assertIsString($json);
    }
}
