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
    public function standardJsonResponse(int $httpCode,bool $success, $messageCode, $data = null, $errorCode = null)
    {
        /* --- Get message from message code --- */
        $arraySysMsg = (new SystemMessage())->getArrayMessageSysEnKh($messageCode);
        $msgSys = $arraySysMsg['sys'];
        $msgEn = $arraySysMsg['en'];
        $msgKh = $arraySysMsg['kh'];

        /* --- Log request info to file --- */
        FileLog::logRequest($messageCode, $success, $httpCode, $errorCode);

        /* --- Return JSON response --- */
        return response()->json(StandardJsonFormat::getMainFormat(
            [$success, $messageCode, $msgSys, $msgEn, $msgKh, $data, $errorCode]
        ), $httpCode);
    }

    /**
     * Standard JSON Response for Exceptions
     *
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function standardJsonExceptionResponse (\Exception $exception)
    {
        /* --- case when token is invalid or not found --- */
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
        /* --- case when token is expired --- */
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
        /* --- case when cannot find user in the database --- */
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
