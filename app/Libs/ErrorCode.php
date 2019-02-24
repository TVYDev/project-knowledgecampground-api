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
    const ERR_CODE_UNAUTHORIZED     = 'E230100';
    const ERR_CODE_UNAUTHENTICATED  = 'E230101';
    const ERR_CODE_LOGIN_FAILED     = 'E230102';

    const ERR_CODE_VALIDATION       = 'E230200';

    const ERR_CODE_TOKEN_INVALID    = 'E230300';
    const ERR_CODE_TOKEN_EXPIRED    = 'E230301';
    const ERR_CODE_TOKEN_TRASHED    = 'E230302';
    const ERR_CODE_TOKEN_BLACKLISTED= 'E230303';

    const ERR_CODE_EXCEPTION        = 'E230900';
}