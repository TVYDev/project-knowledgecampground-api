<?php


namespace App\Libs;


class MessageCode
{
    const PREFIX_SUCCESS_MSG = 'KC_MSG_SUCCESS__';
    const PREFIX_ERROR_MSG = 'KC_MSG_ERROR__';

    private static function getGeneratedCodeAction ($action)
    {
        return strtoupper(str_replace(' ', '_', $action));
    }

    private static function getMessageCode ($prefix, $action)
    {
        return $prefix . self::getGeneratedCodeAction($action);
    }

    public static function msgSuccess ($action)
    {
        return self::getMessageCode(self::PREFIX_SUCCESS_MSG, $action);
    }

    public static function msgError ($action)
    {
        return self::getMessageCode(self::PREFIX_ERROR_MSG, $action);
    }
}
