<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'public_id',
        'title',
        'is_draft',
        'is_active',
        'is_deleted',
        'user__id',
        'posted_at'
    ];

    protected $hidden = [
        'id', 'user__id', 'posted_at'
    ];

    /**
     * Relationship One-to-One with QuestionDescription
     * Get question_description the this question owns
     */
    public function questionDescription()
    {
        return $this->hasOne('App\QuestionDescription','question__id');
    }

    /**
     * Relationship One-to-Many with User
     * Get user that owns this question
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }
}
