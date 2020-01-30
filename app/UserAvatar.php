<?php

namespace App;

use App\Http\Support\Supporter;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class UserAvatar extends Model
{
    protected $table = 'user_avatars';

    protected $fillable = [
        'seed',
        'default_avatar_url',
        'is_using_default',
        'is_active',
        'is_deleted',
        'img_url'
    ];

    protected $hidden = [
        'id', 'user__id',
    ];

    protected $sharedUserAvatar = '\svg\default_avatars\shared_avatar.svg';

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
            $relativeUrl = '/svg/default_avatars/'.$seed.'.svg';

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
            $this['default_avatar_url'] = $this->getSharedAvatarUrl();
        }finally {
            return $this;
        }
    }

    public function getSharedAvatarUrl ()
    {
        return (new Supporter())->getFileUrl($this->sharedUserAvatar);
    }

    public function getActiveUserAvatarUrl ($user)
    {
        $avatarUrl = null;
        if($user){
            $avatar = $user->userAvatar()->where('is_active', true)->first();
            if($avatar){
                if($avatar->is_using_default){
                    $avatarUrl = (new Supporter())->getFileUrl($avatar->default_avatar_url);
                }else{
                    $avatarUrl = (new Supporter())->getFileUrl($avatar->img_url);
                }
            }else{
                $avatarUrl = $this->getSharedAvatarUrl();
            }
        }else{
            $avatarUrl = $this->getSharedAvatarUrl();
        }
        return $avatarUrl;
    }
}
