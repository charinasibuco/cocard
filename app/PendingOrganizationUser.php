<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingOrganizationUser extends Model
{
    protected $table  		= 'pending_organization_user';
    protected $fillable		= ['id','name', 'contact_person','position','contact_number',
    						'email','scheme','logo','password','url','status'];


    public function Organization(){
    	return $this->belongsTo(Organization::class);
    }

    public function User(){
    	return $this->hasOne(User::class);
    }

}
