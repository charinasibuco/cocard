<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table 	= 'permissions';
    protected $fillable = ['title','description'];

    public function Roles()
    {
    	return $this->belongsToMany(Role::class, 'role_permissions');
    }  
    public function RolePermission()
    {
    	return $this->hasMany(RolePermission::class);
    }
}
