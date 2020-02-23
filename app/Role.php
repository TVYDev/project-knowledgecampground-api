<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'is_active',
        'is_deleted',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'id',
        'pivot' //exclude immediate table of many-to-many relationship
    ];

    /**
     * Relationship Many-to-Many with User (Immediate table user_role_mappings)
     * Get one or more users that belong to this roles
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_role_mappings','role__id','user__id')
            ->withPivot('is_active', 'is_deleted', 'created_by', 'updated_by')
            ->withTimestamps();
    }
}
