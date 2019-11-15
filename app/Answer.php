<?php

namespace App;

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
}
