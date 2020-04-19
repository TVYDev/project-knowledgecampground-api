<?php


namespace App\Libs;


class StandardJsonFormat
{
    public static function getMainFormat (array $param)
    {
        return [
            'success'       => $param[0],
            'message_code'  => $param[1],
            'message_sys'   => $param[2],
            'message_en'    => $param[3],
            'message_kh'    => $param[4],
            'data'          => $param[5],
            'error_code'     => $param[6],
            'meta'          => [
                'program'   => 'KnowledgeCampground_API',
                'version'   => '1.0.0'
            ]
        ];
    }

    public static function getAccessTokenFormat (array $param)
    {
        return [
            'access_token'  => $param[0],
            'token_type'    => 'bearer',
            'expire_in'     => auth()->factory()->getTTL() * 60 . ' seconds'
        ];
    }
}
