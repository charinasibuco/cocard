<?php

namespace App;

trait HasRoles{

	public function Roles(){
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function assignRole($role)
    {
        return $this->roles()->save(
                Role::whereTitle($role)->firstOrFail()
            );
    }
    public function hasRole($role)
    {
        if(is_string($role)){
            return $this->roles->contains('title', $role);
        }

        return !! $role->intersect($this->roles)->count();
    }

    public function getRole(){
        $string     = '';
        foreach($this->roles as $role){
            $string .= $role->title . ' ';
        }
        return $string;
    }

}