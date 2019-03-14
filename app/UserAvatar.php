<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAvatar extends Model
{
    protected $table = 'user_avatars';
    public $timestamps = false;

    protected $fillable = [
        'first_initial',
        'bg_color_hex',
        'side_color_hex',
        'stroke_color_hex',
        'is_active',
        'img_url'
    ];

    protected $hidden = [
        'id', 'user__id',
    ];

    /**
     * Get the user that owns the userAvatar
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }

    public function generateColorHex ()
    {
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        return $color;
    }
}
