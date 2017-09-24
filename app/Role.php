<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table    = 'roles';
    protected $fillable = ['title','description'];

    public function Permission()
    {
    	return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function RolePermission()
    {
    	return $this->hasMany(RolePermission::class);
    }

     public function Users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function givePermissionTo(Permission $permission)
    {
        return $this->permission()->save($permission);
    }

    public function attachPermission($permission){
        return $this->permission()->attach($permission);
    }

    public function detachPermission($permission){

        return $this->permission()->detach($permission);

    }
}
