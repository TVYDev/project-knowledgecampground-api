<?php

namespace App;

use App\Http\Support\Supporter;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'replies';

    protected $fillable = [
        'public_id',
        'body',
        'is_active',
        'is_deleted',
        'user__id',
        'comment_id'
    ];

    protected $hidden = [
        'id',
        'user__id'
    ];

    /**
     * Relationship One-to-Many with User
     * Get user that owns this reply
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }

    /**
     * Relationship One-to-Many with Comment
     * Get comment that owns this reply
     */
    public function comment()
    {
        return $this->belongsTo('App\Comment', 'comment__id');
    }

    /********************************************************************************
     * Helper function for Reply Model
     ********************************************************************************/
    public function getListPostedRepliesOfComment ($commentPublicId)
    {
        try
        {
            $supporter = new Supporter();
            $comment = Comment::where('public_id', $commentPublicId)->first();

            if(isset($comment))
            {
                $replies = $comment->replies;
                foreach ($replies as $reply)
                {
                    $reply['readable_time_en'] = $supporter->getHumanReadableActionDateAsString($reply->created_at, $reply->updated_at);
                    $reply['readable_time_kh'] = $supporter->getHumanReadableActionDateAsString($reply->created_at, $reply->updated_at);
                    $reply['author_name'] = $comment->user()->pluck('name')->first();
                    $reply['author_id'] = $comment->user()->pluck('id')->first();

                    $author = User::find($reply['author_id']);
                    $reply['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);
                }

                return $replies;
            }
            return null;
        }
        catch(\Exception $exception)
        {
            return null;
        }
    }
}
