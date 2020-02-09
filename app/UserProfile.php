<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user__id',
        'is_active',
        'is_deleted',
        'full_name',
        'country__id',
        'location',
        'position',
        'about_me',
        'website_link',
        'facebook_link',
        'twitter_link',
        'telegram_link'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * Relationship One-to-Many with Country
     * Get country of this user profile
     */
    public function country()
    {
        return $this->belongsTo('App\Country', 'country__id');
    }

    /**
     * Relationship One-to-One with User
     * Get user that owns this profile
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user__id');
    }
}
