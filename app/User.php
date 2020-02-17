<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'public_id', 'google_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'password', 'remember_token', 'password1', 'password2', 'password3'
    ];

    /**
     * getKey() => return the primary key of the table users which is "id"
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute ($password)
    {
        if(!empty($password))
        {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    /**
     * Relationship One-to-One with UserAvatar
     * Get user_avatar that this user owns
     */
    public function userAvatar()
    {
        return $this->hasOne('App\UserAvatar','user__id');
    }

    /**
     * Relationship One-to-One with UserProfile
     * Get user_profile that this use owns
     */
    public function userProfile()
    {
        return $this->hasOne('App\UserProfile', 'user__id');
    }

    /**
     * Relationship One-to-Many with Question
     * Get questions that this user owns
     */
    public function questions()
    {
        return $this->hasMany('App\Question', 'user__id');
    }

    /**
     * Relationship One-to-Many with Answer
     * Get answers that this user owns
     */
    public function answers()
    {
        return $this->hasMany('App\Answer', 'user__id');
    }

    /**
     * Relationship One-to-Many with Comment
     * Get comments that this user owns
     */
    public function comments()
    {
        return $this->hasMany('App\Comment', 'user__id');
    }

    /**
     * Relationship One-to-Many with Reply
     * Get replies that this user owns
     */
    public function replies ()
    {
        return $this->hasMany('App\Reply', 'user__id');
    }
}
