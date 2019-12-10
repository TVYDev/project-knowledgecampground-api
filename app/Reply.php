<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'replies';

    protected $fillable = [
        'public_id',
        'body',
        'is_active',
        'is_deleted',
        'user__id',
        'comment_id'
    ];

    protected $hidden = [
        'id',
        'user__id'
    ];

    /**
     * Relationship One-to-Many with User
     * Get user that owns this reply
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }

    /**
     * Relationship One-to-Many with Comment
     * Get comment that owns this reply
     */
    public function comment()
    {
        return $this->belongsTo('App\Comment', 'comment__id');
    }
}
