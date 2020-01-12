<?php

namespace App;

use App\Http\Support\Supporter;
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
        'subject__id',
        'posted_at'
    ];

    protected $hidden = [
        'id', 'user__id', 'subject__id',
        'pivot' //exclude immediate table of many-to-many relationship
    ];

    /**
     * Relationship One-to-One with QuestionDescription
     * Get question_description that this question owns
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

    /**
     * Relationship One-to-Many with Subject
     * Get subject that owns this question
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject__id');
    }

    /**
     * Relationship Many-to-Many with Tag (Immediate table = question_tag_mappings)
     * Get one or more tags that belong to this question
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'question_tag_mappings', 'question__id', 'tag__id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Polymorphic One-to-Many relationship with Comment
     *
     * Get all of the question's comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}
