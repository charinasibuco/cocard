<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonationList extends Model
{
    protected $table    = 'donation_list';
    protected $fillable = ['donation_category_id','name','description','recurring','status'];

    public function Donation()
    {
        return $this->hasMany(Donation::class);
    }
    public function DonationCategory()
    {
        return $this->belongsTo(DonationCategory::class);
    }
}
