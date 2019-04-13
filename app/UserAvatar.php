<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class UserAvatar extends Model
{
    protected $table = 'user_avatars';
    public $timestamps = false;

    protected $fillable = [
        'seed',
        'default_avatar_url',
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

    public function generateDefaultUserAvatar ()
    {
        $seed = random_int(1,10000000);
        $sharedAvatarUrl = '\svg\default_avatars\shared_avatar.svg';
        try
        {
            $relativeUrl = '\svg\default_avatars\\'.$seed.'.svg';
            $svgAvatarUrl = getcwd().$relativeUrl;

            $url = 'https://avatars.dicebear.com/v2/jdenticon/'.$seed.'.svg';

            $http = new Client();

            $response = $http->request('GET', $url,[
                'headers' => [
                    'Content-Type' => 'image/svg+xml'
                ],
                'sink' => $svgAvatarUrl
            ]);

            return [
                'seed' => $seed,
                'avatar_url' => $relativeUrl
            ];
        }
        catch(\Exception $exception)
        {
            return [
                'seed' => $seed,
                'avatar_url' => $sharedAvatarUrl
            ];
        }
    }
}
