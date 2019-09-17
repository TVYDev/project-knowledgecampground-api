<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'public_id',
        'name_en',
        'name_kh',
        'description_en',
        'description_kh',
        'img_url',
        'is_active'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * Relationship One-to-Many with Question
     * Get questions that belong to this subject
     */
    public function questions ()
    {
        return $this->hasMany('App\Question', 'subject__id');
    }

    /**
     * Relationship One-to-Many with Tag
     * Get one or more tags that belong to this subject
     */
    public function tags ()
    {
        return $this->hasMany('App\Tag', 'subject__id');
    }
}
