<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    use JsonResponse;

    public function getUserAvatar ()
    {
        try
        {
            $userAvatar = auth()->user()->userAvatar;

            $userAvatar['name'] = $userAvatar->user()->pluck('name')->first();

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                null,
                $userAvatar
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
