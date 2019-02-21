<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 19-Feb-19
 * Time: 12:40 PM
 */

namespace App\Libs;


use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

trait JsonResponse
{
    public function standardJsonResponse(int $httpCode,bool $success, $message, $data = null, $errorCode = null){
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
}