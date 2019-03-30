<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 08-Mar-19
 * Time: 12:10 PM
 */

namespace App\Libs;


class HttpStatusCode
{
    /********************************
     * 2xx Success
     ********************************/
    const SUCCESS_OK            = 200;
    const SUCCESS_CREATED       = 201;
    const SUCCESS_NO_CONTENT    = 204;
    /********************************/



    /********************************
     * 4xx Client Error
     ********************************/
    const ERROR_BAD_REQUEST     = 400;
    const ERROR_UNAUTHORIZED    = 401;
    const ERROR_FORBIDDEN       = 403;
    const ERROR_NOT_FOUND       = 404;
    const ERROR_NOT_ACCEPTABLE  = 406;
    /********************************/



    /********************************
     * 5xx Server Error
     ********************************/
    const ERROR_INTERNAL_SERVER_ERROR = 500;
    /********************************/
}