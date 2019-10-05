<?php
/**
 * Created by PhpStorm.
 * User: vannyou.tan
 * Date: 19-Feb-19
 * Time: 1:12 PM
 */

namespace App\Libs;


class ErrorCode
{
    /******************************************
     * Error code of http request
     ******************************************/
    const ERR_CODE_UNAUTHORIZED     = 'E230100';
    const ERR_CODE_UNAUTHENTICATED  = 'E230101';
    const ERR_CODE_LOGIN_FAILED     = 'E230102';
    /******************************************/



    /******************************************
     * Error code of validation
     ******************************************/
    const ERR_CODE_VALIDATION       = 'E230200';
    /******************************************/



    /******************************************
     * Error code of token
     ******************************************/
    const ERR_CODE_TOKEN_INVALID    = 'E230300';
    const ERR_CODE_TOKEN_EXPIRED    = 'E230301';
    const ERR_CODE_TOKEN_TRASHED    = 'E230302';
    const ERR_CODE_TOKEN_BLACKLISTED= 'E230303';
    const ERR_CODE_TOKEN_NOT_PROVIDED = 'E230304';
    const EER_CODE_JWT_EXCEPTION    = 'E230309';
    /******************************************/



    /******************************************
     * Error code of data not exist
     ******************************************/
    const ERR_CODE_DATA_NOT_EXIST   = 'E230400';



    /******************************************
     * Error code of exception
     ******************************************/
    const ERR_CODE_EXCEPTION        = 'E230900';
    /******************************************/
}
