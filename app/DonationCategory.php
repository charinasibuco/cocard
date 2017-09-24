<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonationCategory extends Model
{
    protected $table    = 'donation_category';
    protected $fillable = ['organization_id','name','description','status'];

   
    public function DonationList()
    {
        return $this->hasMany(DonationList::class);
    }
}
