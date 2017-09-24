<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class QueuedMessageModel extends Model
{
    protected $table    = 'queued_message';
    protected $fillable = ['event_id','volunteer_group_id','subject','message','email','event_date','reminder_date','status'];

    public function Event(){
    	return $this->belongsTo(Event::class);
    }
    public function Participant(){
        return $this->belongsTo(Participant::class);
    }

    public function Volunteer(){
    	return $this->hasManyThrough(Volunteer::class,VolunteerGroup::class);
    }
    
    public function VolunteerGroup(){
    	return $this->belongsTo(VolunteerGroup::class);
    }

    public function getEvent(){
        return $this->Event->recurring;
    }

    public function getTotalNoOfOccurrenceAttribute(){
        $recurring = $this->Event->recurring;
        $recurring_end_date = $this->parse($this->Event->recurring_end_date);
        $start_date = $this->parse($this->Event->start_date);
        $total_no_of_occurrence = $this->countDiff($recurring,$start_date,$recurring_end_date);
        return $total_no_of_occurrence;
    }

    public function countDiff($recurring,$start_date,$recurring_end_date){
        if($recurring == 1){
           $count = $recurring_end_date->diffInWeeks($start_date);
           return $count;
        }elseif ($recurring == 2) {
           $count = $recurring_end_date->diffInMonths($start_date);
           return $count;
        }elseif($recurring == 3){
            $count =  $recurring_end_date->diffInYears($start_date);
           return $count;
        }
    }

    public function parse($date){
        $parse_date = Carbon::parse($date);
        return $parse_date;
    }

}
