<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    protected $table = 'transaction_details';
    public $timestamps = false;

    protected $fillable = [
        'transaction_id', 'volunteer_id', 'frequency_id', 'event_id', 'status'
    ];
    
    public function Transaction()
    {
    	return $this->belongsTo(Transaction::class);
    } 
    public function Participants()
    {
    	return $this->belongsTo(Participants::class);
    } 
    public function Volunteers()
    {
    	return $this->belongsTo(Volunteers::class);
    } 
    public function Frequency()
    {
    	return $this->belongsTo(Frequency::class);
    }
    public function Event()
    {
    	return $this->belongsTo(Event::class);
    }
}
