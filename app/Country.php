<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'code',
        'name_en',
        'name_kh',
        'is_active'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * Relationship One-to-Many with UserProfile
     * Get all user profiles that bind with this country
     */
    public function userProfiles()
    {
        return $this->hasMany('App\UserProfile', 'country__id');
    }

}
