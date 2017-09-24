<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $table    = 'family';
    protected $fillable = ['organization_id', 'name', 'description', 'primary_phone', 'secondary_phone', 'primary_email', 'secondary_email', 'address_1', 'address_2', 'city', 'state', 'zipcode', 'status'];

	public function Volunteer(){
		return $this->hasMany(Volunteer::class);
	}

	public function FamilyMember(){
		return $this->belongsToMany(FamilyMember::class);
	}
}