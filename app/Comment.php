<?php

namespace App;

use App\Http\Support\Supporter;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * Fields in table
     */
    const FIELD_PUBLIC_ID = 'public_id';
    const FIELD_BODY = 'body';
    const FIELD_IS_ACTIVE = 'is_active';
    const FIELD_IS_DELETED = 'is_deleted';
    const FIELD_USER__ID = 'user__id';

    protected $table = 'comments';

    protected $fillable = [
        'public_id',
        'body',
        'is_active',
        'is_deleted',
        'user__id'
    ];

    protected $hidden = [
        'id',
        'user__id'
    ];

    /**
     * Custom Constants
     */
    const COMMENTABLE_QUESTION = 'question';
    const COMMENTABLE_ANSWER = 'answer';

    /**
     * Relationship One-to-Many with User
     * Get user that own this comment
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }

    /**
     * Comment has polymorphic (One-to-Many) relationships with Question & Answer
     *
     * Get the owning commentable model
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship One-to-Many with Reply
     * Get all replies of this comment
     */
    public function replies()
    {
        return $this->hasMany('App\Reply', 'comment__id');
    }

    /**
     * Helpers
     */
    public static function getCommentsOfCommentable ($commentableType, $commentPublicId)
    {
        try
        {
            $supporter = new Supporter();
            $allComments = [];
            $commentable = null;
            if($commentableType == self::COMMENTABLE_QUESTION)
            {
                $commentable = Question::where(self::FIELD_PUBLIC_ID, $commentPublicId)->first();
            }
            elseif ($commentableType == self::COMMENTABLE_ANSWER)
            {
                $commentable = Answer::where(self::FIELD_PUBLIC_ID, $commentPublicId)->first();
            }

            if(isset($commentable))
            {
                $comments = $commentable->comments;
                foreach ($comments as $comment)
                {
                    $comment['readable_time_en'] = $supporter->getHumanReadableActionDateAsString($comment->created_at, $comment->updated_at);
                    $comment['readable_time_kh'] = $supporter->getHumanReadableActionDateAsString($comment->created_at, $comment->updated_at);
                    $comment['author_name'] = $comment->user()->pluck('name')->first();
                    $comment['author_id'] = $comment->user()->pluck('id')->first();

                    $author = User::find($comment['author_id']);
                    $comment['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);

//                    $comment['replies'] = (new Reply())->getListPostedRepliesOfComment($comment->public_id);
                }

                $allComments = $comments;
            }
        }
        catch(\Exception $exception)
        {
            // TODO: Add log
        }

        return $allComments;
    }
}
