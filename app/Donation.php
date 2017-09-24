<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Donation extends Model
{
    protected $table    = 'donation';
    protected $fillable = ['frequency_id','donation_type','donation_category_id','transaction_id','start_date','end_date','no_of_payments','amount', 'note','status', 'donation_list_id'];

    public function DonationList()
    {
        return $this->belongsTo(DonationList::class);
    }
    public function DonationCategory()
    {
        return $this->belongsTo(DonationCategory::class);
    }
    public function Frequency()
    {
        return $this->belongsTo(Frequency::class);
    }
    public function Organizations()
    {
    	return $this->belongsTo(Organizations::class);
    }
      public function Transaction()
    {
    	return $this->hasOne(Transaction::class);
    }
    public function getFormatStartdateAttribute(){
		return Carbon::parse($this->start_date);
	}
    public function getFormatEnddateAttribute(){
		return Carbon::parse($this->end_date);
	}
}
