<?php

namespace App\Http\Controllers;

use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    use JsonResponse;

    /**
     * UserAvatar get information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAvatar ()
    {
        try
        {
            // --- get user_avatar of the user
            $userAvatar = auth()->user()->userAvatar;
            // --- add name as extra information for needed use
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
