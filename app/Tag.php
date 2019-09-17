<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'public_id',
        'name_en',
        'name_kh',
        'description_en',
        'description_kh',
        'img_url',
        'is_active',
        'subject__id'
    ];

    protected $hidden = [
        'id', 'subject__id',
        'pivot' //exclude immediate table of many-to-many relationship
    ];

    /**
     * Relationship One-to-Many with Subject
     * Get the only one subject that owns this tag
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject__id');
    }

    /**
     * Relationship Many-to-Many with Question (Immediate table question_tag_mappings)
     * Get one or more questions that belong to this tag
     */
    public function questions()
    {
        return $this->belongsToMany('App\Question', 'question_tag_mappings','tag__id','question__id')
            ->withPivot('is_active')
            ->withTimestamps();
    }
}
