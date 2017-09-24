<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgRole extends Model
{
    protected $table = 'org_roles';
    public $timestamps = false;

    protected $fillable = [
        'organization_id', 'role_id', 'status'
    ];

    public function Role()
    {
        return $this->belongsTo(Role::class);
    }

    public function Organization()
    {
        return $this->belongsTo(User::class);
    }
}
