<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class UserAvatar extends Model
{
    protected $table = 'user_avatars';

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
     * Relationship One-to-One with User
     * Get the user that owns the userAvatar
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }

    public function generateDefaultUserAvatar ()
    {
        $seed = random_int(1,10000000);
        $this['seed'] = $seed;
        try
        {
            $originalUrl = (new ThirdPartyApiUrl())->getApiUrl('jdenticon');
            $url = str_replace('{{PLACEHOLDER}}', $seed, $originalUrl);
            $relativeUrl = '\svg\default_avatars\\'.$seed.'.svg';

            $http = new Client();
            $http->request('GET', $url,[
                'headers' => [
                    'Content-Type' => 'image/svg+xml'
                ],
                'sink' => getcwd().$relativeUrl
            ]);

            $this['default_avatar_url'] = $relativeUrl;
        }
        catch(\Exception $exception)
        {
            $sharedAvatarUrl = '\svg\default_avatars\shared_avatar.svg';
            $this['default_avatar_url'] = $sharedAvatarUrl;
        }finally {
            return $this;
        }
    }
}
