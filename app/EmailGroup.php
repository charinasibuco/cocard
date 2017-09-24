<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailGroup extends Model
{
    protected $table 	= 'email_groups';
    protected $fillable = ['organization_id', 'name', 'details', 'status'];

    public function EmailGroupMember(){
        return $this->hasMany(EmailGroupMember::class);
    }
}
