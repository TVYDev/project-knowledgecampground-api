<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    use JsonResponse;

    public function getGeneratePublicId()
    {
        try
        {
            $availableChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $randomString = '';
            $randomStringLength = 10;

            for ($i=0; $i<$randomStringLength; $i++){
                $randomIndex = rand(0, strlen($availableChars) - 1);
                $randomString .= $availableChars[$randomIndex];
            }

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
}
