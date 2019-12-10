<?php

namespace App\Http\Controllers;

use App\Http\Support\Supporter;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SupportController extends Controller
{
    use JsonResponse;

    protected $supporter;

    public function __construct()
    {
        $this->supporter = new Supporter();
    }

    public function getGeneratePublicId()
    {
        try
        {
            $randomString = $this->supporter->generatePublicId();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                null,
                ['public_id' => $randomString]
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    public function clearCacheValidationRules()
    {
        try
        {
            $rules = (new KCValidate())->getAllKeyValidationRuleNames();
            foreach ($rules as $rule){
                Cache::forget($rule);
            }

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                null
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
