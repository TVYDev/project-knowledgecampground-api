<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
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
        return $this->hasMany('App\Comment', 'comment__id');
    }
}
