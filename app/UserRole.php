<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'role_id', 'original_user_id', 'status'
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
