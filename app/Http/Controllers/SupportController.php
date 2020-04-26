<?php

namespace App\Http\Controllers;

use App\Http\Support\Supporter;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\KCValidate;
use App\Libs\MessageCode;
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

    /**
     * Generate Public Id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGeneratePublicId()
    {
        try
        {
            $randomString = $this->supporter->generatePublicId();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('public id generated'),
                ['public_id' => $randomString]
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }

    /**
     * Clear Cache Validation Rules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClearCacheValidationRules()
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
                MessageCode::msgSuccess('cache validation rules cleared')
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
