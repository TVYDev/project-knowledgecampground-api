<?php


namespace App\Libs;


class MessageCode
{
    const PREFIX_SUCCESS_MSG = 'KC_MSG_SUCCESS__';
    const PREFIX_ERROR_MSG = 'KC_MSG_ERROR__';

    public static function msgSuccess ($action)
    {
        return self::PREFIX_SUCCESS_MSG . strtoupper($action);
    }

    public static function msgError ($action)
    {
        return self::PREFIX_ERROR_MSG . strtoupper($action);
    }
}
