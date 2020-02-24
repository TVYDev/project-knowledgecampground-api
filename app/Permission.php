<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

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
     * Relationship Many-to-Many with Role (Immediate table role_permission_mappings)
     * Get one or more roles that belong to this permission
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_permission_mappings','permission__id','role__id')
            ->withPivot('is_active', 'is_deleted', 'created_by', 'updated_by')
            ->withTimestamps();
    }
}
