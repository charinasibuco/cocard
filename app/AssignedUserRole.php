<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignedUserRole extends Model
{
    protected $table = 'assigned_user_roles';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'role_id', 'status'
    ];

    public function Role()
    {
        return $this->belongsTo(Role::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}