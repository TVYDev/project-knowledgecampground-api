<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 19-Feb-19
 * Time: 12:40 PM
 */

namespace App\Libs;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

trait JsonResponse
{
    public function standardJsonResponse(int $httpCode,bool $success, $message, $data = null, $errorCode = null){
        $path = $_SERVER['PATH_INFO'];
        $fromRemote = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

        $inputs = Input::all();
        $filteredInputs = array_filter($inputs, function($key) {
            return strpos($key, 'password') === false;
        }, ARRAY_FILTER_USE_KEY);

        $context = [
            $path,
            $fromRemote,
            count($filteredInputs) > 0 ? json_encode($filteredInputs) : null
        ];

        if($success){
            Log::info($message, $context);
        }else{
            if($httpCode === 500)
                Log::critical($errorCode.':'.$message, $context);
            else
                Log::error($errorCode.':'.$message, $context);
        }
        return response()->json([
            'success'   => $success,
            'message'   => $message,
            'data'      => $data,
            'errorCode' => $errorCode,
            'meta'      => [
                'program'   => 'KnowledgeCommunity_API',
                'version'   => '1.0'
            ]
        ],$httpCode);
    }

    public function standardJsonExceptionResponse (\Exception $exception)
    {
        if($exception instanceof TokenInvalidException)
        {
            return $this->standardJsonResponse(
                400,
                false,
                'Invalid Token',
                null,
                ErrorCode::ERR_CODE_TOKEN_INVALID
            );
        }
        else if($exception instanceof TokenExpiredException)
        {
            return $this->standardJsonResponse(
                400,
                false,
                'Expired Token',
                null,
                ErrorCode::ERR_CODE_TOKEN_EXPIRED
            );
        }
        else if($exception instanceof ModelNotFoundException){
            return $this->standardJsonResponse(
                401,
                false,
                'Unauthenticated user',
                null,
                ErrorCode::ERR_CODE_UNAUTHENTICATED
            );
        }

        return $this->standardJsonResponse(
            500,
            false,
            $exception->getMessage(),
            null,
            ErrorCode::ERR_CODE_EXCEPTION
        );
    }

    public function standardJsonValidationErrorResponse ($errorMessage)
    {
        return $this->standardJsonResponse(
            422,
            false,
            $errorMessage,
            null,
            ErrorCode::ERR_CODE_VALIDATION
        );
    }

    public function standardJsonUnauthorizedResponse ()
    {
        return $this->standardJsonResponse(
            401,
            false,
            'Unauthorized Access',
            null,
            ErrorCode::ERR_CODE_UNAUTHORIZED
        );
    }

    public function standardLoginFailedResponse ()
    {
        return $this->standardJsonResponse(
            401,
            false,
            'Email or password is incorrect',
            null,
            ErrorCode::ERR_CODE_LOGIN_FAILED
        );
    }
}