<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FamilyMember extends Model
{
    protected $table    = 'family_members';
    protected $fillable = ['family_id', 'user_id', 'first_name', 'last_name', 'middle_name', 'birthdate', 'gender', 'allergies', 'img', 'relationship', 'additional_info', 'child_number', 'status'];

	public function User(){
		return $this->hasMany(User::class);
	}

	public function Family(){
		return $this->hasMany(Family::class);
	}

    public function getFormatBirthdateAttribute(){
		return Carbon::parse($this->birthdate);
	}
}
