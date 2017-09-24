<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = "staffs";
    protected $fillable = ["first_name","last_name","role","organization_id","email","contact_number","status"];

    public function getFillable(){
        return $this->fillable;
    }

    public function getFullNameAttribute(){
        return $this->attributes["first_name"]." ".$this->attributes["last_name"];
    }
}
