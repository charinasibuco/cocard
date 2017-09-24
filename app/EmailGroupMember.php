<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailGroupMember extends Model
{
    protected $table 	= 'email_group_members';
    protected $fillable	= ['email_group_id', 'user_id', 'name', 'email', 'status','gender', 'marital_status', 'birthdate'];

    public function User(){
    	return $this->hasOne(User::class);
    }
    public function EmailGroup(){
    	return $this->hasOne(EmailGroup::class);
    }
    public function Email(){
    	return $this->hasOne(Email::class);
    }
}
