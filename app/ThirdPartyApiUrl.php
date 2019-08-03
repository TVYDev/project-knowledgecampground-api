<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyApiUrl extends Model
{
    protected $table = 'third_party_api_urls';

    protected $fillable = [
        'key', 'value', 'is_active', 'description'
    ];

    public function getApiUrl ($key){
        try {
            $api = self::where('key', $key)
                ->where('is_active', true)
                ->first();

            return $api->value;
        }catch(\Exception $exception){
            throw $exception;
        }
    }
}
