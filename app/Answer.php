<?php

namespace App;

use App\Http\Support\Supporter;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answers';

    protected $fillable = [
        'public_id',
        'is_draft',
        'is_active',
        'is_deleted',
        'is_accepted',
        'accepted_at',
        'posted_at',
        'user__id',
        'question__id'
    ];

    protected $hidden = [
        'id', 'user__id', 'question__id'
    ];

    /**
     * Relationship One-to-Many with User
     * Get user that owns this answer
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }

    /**
     * Relationship One-to-One with AnswerDescription
     * Get answer_description that this answer owns
     */
    public function answerDescription ()
    {
        return $this->hasOne('App\AnswerDescription', 'answer__id');
    }

    /**
     * Polymorphic One-to-Many relationship with Comment
     *
     * Get all of the answer's comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    /**
     * Helpers
     */
    public static function getAnswerInfo ($answer)
    {
        try
        {
            $supporter = new Supporter();
            $answer['readable_time_en'] = $supporter->getHumanReadableActionDateAsString($answer->posted_at, $answer->updated_at, Supporter::ANSWER_ACTION);
            $answer['readable_time_kh'] = $supporter->getHumanReadableActionDateAsString($answer->posted_at, $answer->updated_at, Supporter::ANSWER_ACTION);
            $answer['author_name'] = $answer->user()->pluck('name')->first();
            $answer['author_id'] = $answer->user()->pluck('id')->first();

            $author = User::find($answer['author_id']);
            $answer['avatar_url'] = (new UserAvatar())->getActiveUserAvatarUrl($author);
        }
        catch(\Exception $exception)
        {
            // TODO: Add log
        }
        return $answer;
    }
}
