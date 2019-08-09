<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemMessage extends Model
{
    protected $table = 'system_messages';

    protected $fillable = [
        'code','message_en','message_kh','type','is_active','description'
    ];

    public function getArrayMessageSysEnKh($code)
    {
        $result = [
            'sys'=> null,
            'en' => null,
            'kh' => null
        ];
        try
        {
            $msg = self::where('code', $code)
                ->where('is_active',true)
                ->first();

            if($msg){
                $result = [
                    'sys'   => is_null($msg->message_sys) || empty($msg->message_sys) ? null : $msg->message_sys,
                    'en'    => is_null($msg->message_en) || empty($msg->message_en) ? null : $msg->message_en,
                    'kh'    => is_null($msg->message_kh) || empty($msg->message_kh) ? null : $msg->message_kh
                ];
            }

            return $result;
        }
        catch(\Exception $exception)
        {
            return $result;
        }
    }
}
