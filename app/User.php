<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const KEY_JWT_CLAIM_ACCESS = 'access';
    const JWT_CLAIM_ACCESS_GENERAL = 'general_2306';
    const JWT_CLAIM_ACCESS_RESET = 'reset_2306';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'public_id', 'provider_user_id', 'provider'
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
        return [self::KEY_JWT_CLAIM_ACCESS => self::JWT_CLAIM_ACCESS_GENERAL];
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

    /**
     * Relationship Many-to-Many with Role (Immediate table = user_role_mappings)
     * Get one or more roles that belong to this user
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'user_role_mappings', 'user__id', 'role__id')
            ->withPivot('is_active', 'is_deleted', 'created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Relationship Many-to-Many with Question (Immediate table = user_question_votes)
     * Get one ore more question_vote that this user votes
     */
    public function questionVotes()
    {
        return $this->belongsToMany('App\Question', 'user_question_votes', 'user__id', 'question__id')
            ->withPivot('vote', 'created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Relationship Many-to-Many with Answer (Immediate table = user_answer_votes)
     * Get one or more answer_vote that this user votes
     */
    public function answerVotes()
    {
        return $this->belongsToMany('App\Answer', 'user_answer_votes', 'user__id', 'answer__id')
            ->withPivot('vote', 'created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Relationship Many-to-Many with Question (Immediate table = user_question_favorites)
     * Get one or more question_favorites of this user
     */
    public function questionFavorites()
    {
        return $this->belongsToMany('App\Question', 'user_question_favorites', 'user__id', 'question__id')
            ->withPivot('created_by', 'updated_by')
            ->withTimestamps();
    }

    /**
     * Relationship One-to-Many with UserModelActivity
     * Get User model-activity of this user
     */
    public function modelActivities()
    {
        return $this->hasMany('App\UserModelActivity', 'user__id');
    }
}
