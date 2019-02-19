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
    const ERR_CODE_UNAUTHORIZED     = 'E23001';

    const ERR_CODE_VALIDATION       = 'E23002';

    const ERR_CODE_TOKEN_INVALID    = 'E23003';
    const ERR_CODE_TOKEN_EXPIRED    = 'E23004';
    const ERR_CODE_TOKEN_TRASHED    = 'E23005';

    const ERR_CODE_EXCEPTION        = 'E23009';
}