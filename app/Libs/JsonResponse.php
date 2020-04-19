<?php
/**
 * Created by PhpStorm.
 * User: Vannyou TANG
 * Date: 19-Feb-19
 * Time: 12:40 PM
 */

namespace App\Libs;


use App\Exceptions\KCValidationException;
use App\SystemMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use App\Log AS DBLog;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

trait JsonResponse
{
    /**
     * Generic method for returning standard JSON response
     *
     * @param int $httpCode
     * @param bool $success
     * @param $messageCode
     * @param null $data
     * @param null $errorCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardJsonResponse(int $httpCode,bool $success, $messageCode, $data = null, $errorCode = null){
        // --- prepare data for using in the log
        $inputs = Input::all(); // inputs of request
        $filteredInputs = array_filter($inputs, function($key) { // exclude password from log data
            return strpos($key, 'password') === false;
        }, ARRAY_FILTER_USE_KEY);

        // --- structure data for logging
        $context = [
            count($filteredInputs) > 0 ? json_encode($filteredInputs) : null
        ];

        // --- get message from message_code
        $arraySysMsg = (new SystemMessage())->getArrayMessageSysEnKh($messageCode);
        $msgSys = $arraySysMsg['sys'];
        $msgEn = $arraySysMsg['en'];
        $msgKh = $arraySysMsg['kh'];

        // --- do logging according to status of request
        if($success){
            Log::info($msgSys, $context);
        }else{
            if($httpCode === HttpStatusCode::ERROR_INTERNAL_SERVER_ERROR)
                Log::critical($errorCode.':'.$msgSys, $context);
            else
                Log::error($errorCode.':'.$msgSys, $context);
        }

        // --- do response JSON
        return response()->json([
            'success'       => $success,
            'message_code'  => $messageCode,
            'message_sys'   => $msgSys,
            'message_en'    => $msgEn,
            'message_kh'    => $msgKh,
            'data'          => $data,
            'error_code'     => $errorCode,
            'meta'          => [
                'program'   => 'KnowledgeCampground_API',
                'version'   => '1.0.0'
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
            DBLog::error($exception);
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'KC_MSG_ERROR__INVALID_TOKEN',
                null,
                ErrorCode::ERR_CODE_TOKEN_INVALID
            );
        }
        // --- case when token is expired
        else if($exception instanceof TokenExpiredException)
        {
            DBLog::error($exception);
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'KC_MSG_ERROR__EXPIRED_TOKEN',
                null,
                ErrorCode::ERR_CODE_TOKEN_EXPIRED
            );
        }
        /* --- case when inputs validation fails --- */
        else if($exception instanceof  KCValidationException)
        {
            DBLog::error($exception);
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_NOT_ACCEPTABLE,
                false,
                $exception->getMessage(),
                null,
                ErrorCode::ERR_CODE_VALIDATION
            );
        }
        // --- case when cannot find user in the database
        else if($exception instanceof ModelNotFoundException)
        {
            DBLog::error($exception);
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_UNAUTHORIZED,
                false,
                'KC_MSG_ERROR__UNAUTHENTICATED_USER',
                null,
                ErrorCode::ERR_CODE_UNAUTHENTICATED
            );
        }
        else if($exception instanceof JWTException)
        {
            DBLog::error($exception);
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_BAD_REQUEST,
                false,
                'KC_MSG_ERROR__JWT_EXCEPTION',
                null,
                ErrorCode::ERR_CODE_JWT_EXCEPTION
            );
        }
        else{
            DBLog::critical($exception);
            return $this->standardJsonResponse(
                HttpStatusCode::ERROR_INTERNAL_SERVER_ERROR,
                false,
                $exception->getMessage(),
                null,
                ErrorCode::ERR_CODE_EXCEPTION
            );
        }
    }
}
