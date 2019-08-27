<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionDescription extends Model
{
    protected $table = 'question_descriptions';

    protected $fillable = [
        'data',
        'is_active',
        'is_deleted'
    ];

    protected $hidden = [
        'id', 'question__id'
    ];

    /**
     * Relationship One-to-One with Question
     * Get the question that own this question_description
     */
    public function question()
    {
        return $this->belongsTo('App\Question','question__id');
    }
}
