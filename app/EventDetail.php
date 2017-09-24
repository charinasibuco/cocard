<?php

namespace App;

use App\Event;

use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    protected $table    = 'event_details';
    protected $fillable = ['event_id', 'description', 'capacity', 'fee', 'start_date', 'end_date', 'reminder_date', 'volunteer_number'];

    public function Event(){
	    return $this->hasOne(Event::class);
	}

}