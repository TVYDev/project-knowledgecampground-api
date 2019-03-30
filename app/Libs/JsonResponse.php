<?php
/**
 * Created by PhpStorm.
 * User: Vannyou TANG
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
    /**
     * Generic method for returning standard JSON response
     *
     * @param int $httpCode
     * @param bool $success
     * @param $message
     * @param null $data
     * @param null $errorCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardJsonResponse(int $httpCode,bool $success, $message, $data = null, $errorCode = null){
        // --- prepare data for using in the log
        $path = $_SERVER['PATH_INFO']; // path of request
        $fromRemote = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT']; // IP address of request
        $inputs = Input::all(); // inputs of request
        $filteredInputs = array_filter($inputs, function($key) { // exclude password from log data
            return strpos($key, 'password') === false;
        }, ARRAY_FILTER_USE_KEY);

        // --- structure data for logging
        $context = [
            $path,
            $fromRemote,
            count($filteredInputs) > 0 ? json_encode($filteredInputs) : null
        ];

        // --- do logging according to status of request
        if($success){
            Log::info($message, $context);
        }else{
            if($httpCode === HttpStatusCode::ERROR_INTERNAL_SERVER_ERROR)
                Log::critical($errorCode.':'.$message, $context);
            else
                Log::error($errorCode.':'.$message, $context);
        }

        // --- do response JSON
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

    /**
     * Standard JSON Response for Exceptions
     *
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardJsonExceptionResponse (\Exception $exception)
    {
        // --- case when token is invalid or not found
        if($exception instanceof TokenInvalidException)
        {
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'Invalid Token',
                null,
                ErrorCode::ERR_CODE_TOKEN_INVALID
            );
        }
        // --- case when token is expired
        else if($exception instanceof TokenExpiredException)
        {
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'Expired Token',
                null,
                ErrorCode::ERR_CODE_TOKEN_EXPIRED
            );
        }
        // --- case when cannot find user in the database
        else if($exception instanceof ModelNotFoundException){
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_UNAUTHORIZED,
                false,
                'Unauthenticated user',
                null,
                ErrorCode::ERR_CODE_UNAUTHENTICATED
            );
        }
        // --- case when server is not running
        else{
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_INTERNAL_SERVER_ERROR,
                false,
                $exception->getMessage(),
                null,
                ErrorCode::ERR_CODE_EXCEPTION
            );
        }
    }

    /**
     * Standard JSON Response for user login failure
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardLoginFailedResponse ()
    {
        return $this->standardJsonResponse(
            HttpStatusCode::ERROR_UNAUTHORIZED,
            false,
            'Email or password is incorrect',
            null,
            ErrorCode::ERR_CODE_LOGIN_FAILED
        );
    }

    /**
     * Standard JSON Response for error when user is unauthorized to request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardJsonUnauthorizedResponse ()
    {
        return $this->standardJsonResponse(
            HttpStatusCode::ERROR_UNAUTHORIZED,
            false,
            'Unauthorized Access',
            null,
            ErrorCode::ERR_CODE_UNAUTHORIZED
        );
    }

    /**
     * Standard JSON Response for error validation on inputs
     *
     * @param $errorMessage
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardJsonValidationErrorResponse ($errorMessage)
    {
        return $this->standardJsonResponse(
            HttpStatusCode::ERROR_NOT_ACCEPTABLE,
            false,
            $errorMessage,
            null,
            ErrorCode::ERR_CODE_VALIDATION
        );
    }
}