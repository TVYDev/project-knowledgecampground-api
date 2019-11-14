<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerDescription extends Model
{
    protected $table = 'answer_descriptions';

    protected $fillable = [
        'data',
        'is_active',
        'is_deleted'
    ];

    protected $hidden = [
        'id', 'answer__id'
    ];

    /**
     * Relationship One-to-One with Answer
     * Get answer that owns this answer
     */
    public function answer ()
    {
        return $this->belongsTo('App\Answer', 'answer__id');
    }
}
