<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Event extends Model
{
    protected $table    = 'event';
    protected $fillable = ['parent_event_id','modify_recurring_month','no_of_repetition','recurring_end_date','original_no_of_repetition','original_recurring_end_date','organization_id', 'name','recurring', 'status','description', 'capacity', 'fee', 'start_date', 'end_date', 'reminder_date', 'volunteer_number','pending'];


    public function Organization(){
	return $this->belongsTo(Organization::class);
	}

	public function TransactionDetails(){
	return $this->hasMany(TransactionDetails::class);
	}

	public function EventDetail(){
	return $this->belongsToMany(EventDetail::class);
	}

	public function Participant(){
	// return $this->belongsToMany(Participant::class);
		return $this->hasMany(Participant::class);
	}
	
	public function QueuedMessageModel(){
		return $this->hasMany(QueuedMessageModel::class);
	}

	public function getAllVolunteerGroupsAttribute(){
		return $this->hasMany(VolunteerGroup::class)->get();
	}

	public function getVolunteerGroupsAttribute(){
		return $this->hasMany(VolunteerGroup::class)->where("status","=","Active")->get();
	}
	public function getPerVolunteerGroupAttribute(){
		return $this->hasMany(VolunteerGroup::class)->where("status","=","Active")->first();
	}

	public function checkUnique($value,$attribute,$volunteer_group_id){
		$count = 0;
		//dd($value,$attribute,$volunteer_group_id);
		$volunteers = Volunteer::where('volunteer_group_id',$volunteer_group_id)->get();
		foreach($volunteers as $volunteer){

			$volunteer = $volunteer->toArray();
			//dd($value,$attribute,$volunteer_group_id,$volunteer);
			for ($i=1; $i < count($value); $i++) { 
				//dd($attribute,$value, $volunteer['email']);
					 // if($volunteer[$attribute[$i]] == $value[$i]){
					//dd($volunteer['name'], $value[1], $volunteer['email'], $value[1],$volunteer['name'], $value[0], $volunteer['email'], $value[0],($volunteer['name']== $value[$i] && $volunteer['email']== $value[$i]));
					if($volunteer['name'] == $value[$i] && $volunteer['email'] == $value[$i -1]){
					$count++;
				}
			}
		}
		return (($count == 0) ? 'true' : 'false');
	}
	public function VolunteerGroup(){
		return $this->hasMany(VolunteerGroup::class);
	}
	public function Volunteers(){
		return $this->hasManyThrough(Volunteer::class,VolunteerGroup::class);
	}
	public function getVolunteerSlotsAttribute(){
		$total_needed = 0;
		$slots_taken = 0;
		foreach($this->volunteer_groups as $volunteer_group){
			$total_needed += $volunteer_group->volunteers_needed;
			$slots_taken += count($volunteer_group->volunteers->where('volunteer_group_status','Approved'));
		}
		return ($total_needed - $slots_taken);
	}

	public function getVolunteersNeededAttribute(){
		$total_needed = 0;
		foreach($this->volunteer_groups as $volunteer_group){
			$total_needed += $volunteer_group->volunteers_needed;
		}
		return $total_needed;
	}
	public function groupByEvent($organization_id,$request){
		return $this->where('organization_id',$organization_id)->whereIn('id',function($query) use ($organization_id,$request){
			$query->select('event_id')->from('volunteer_groups')->whereIn('event_id', function($query) use ($organization_id,$request){
					if(isset($request->user)){
						$now = Carbon::now();
						$date =Carbon::now();
						$event = $query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->where('end_date','>=',$now)->orWhereIn('id',function($query) use($now,$organization_id){
							$query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->where('recurring_end_date', '>', $now);
						});
						$event->where(function($query) use ($now,$event,$organization_id,$date){
							$no_of_repetition = $query->select('*')->from('event')->where('organization_id',$organization_id)->where('no_of_repetition','>','0')->where('status','Active')->get();
							if(count($no_of_repetition) > 0){
								//$query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->where('end_date','>=',$now)->orWhere('recurring_end_date','>=',$now);
								$event->orWhereIn('id',function($query) use($organization_id,$now,$event,$no_of_repetition){
										for ($i=0; $i <count($no_of_repetition); $i++) { 
											$num[$i] = ($no_of_repetition[$i]->no_of_repetition)-1;
											$start_date[$i] = Carbon::parse($no_of_repetition[$i]->start_date);
											//$s = $start_date[$i]->addWeeks($num[$i]);
											$start_date_recurring[$i] = $this->dateReccurring($num[$i],$start_date[$i],$no_of_repetition[$i]->recurring);
											$true[$i] = $this->getNumberofRepetition($start_date_recurring[$i],$now,$no_of_repetition[$i]->id);
										}
										//variable true are ids having start date with > date today
										$query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->whereIn('id',$true)->get();
								});
							}
						})
						->where('organization_id',$organization_id)
						->get();
					}
					else{
							$query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->get();
						}
					});
		});
	}
	public function volunteerGroupByType(){
		$now = Carbon::now();
		return $this->VolunteerGroup()->where('start_date','>',$now)->groupBy('type');
	}
	public function volunteerGroupByTypeAdmin(){
		return $this->VolunteerGroup()->groupBy('type');
	}
	public function volunteerGroupsUnderType($type){
		$now = Carbon::now();
		return $this->VolunteerGroup()->where('type',$type)->where('status','Active')->where('start_date','>',$now)->get();
	}
	public function dateFormat($date){
		if($date == '0000-00-00 00:00:00'){
			$date =$this->VolunteerGroup()->where('status','Active')->orderBy('id','desc')->first()->end_date;
		}
	    return Carbon::parse($date)->format('n/j/Y');
	}
	public function getNowAttribute(){
	    return Carbon::parse()->format('n/j/Y');
	}
	public function voluteerRecurringEndDate($date){
		if($date == '0000-00-00 00:00:00'){
			$date =$this->VolunteerGroup()->where('status','Active')->orderBy('end_date','desc')->first()->end_date;
		}
	    return  $date;
	}
	public function getFormatStartDateAttribute(){
		return Carbon::parse($this->start_date);
	}
	public function getFormatEndDateAttribute(){
		return Carbon::parse($this->end_date);
	}
	public function getFormatReminderDateAttribute(){
		return Carbon::parse($this->reminder_date);
	}
	public function getFormatRecurringEndDateAttribute(){
		return Carbon::parse($this->recurring_end_date);
	}
	public function getFormatOriginalRecurringEndDateAttribute(){
		return Carbon::parse($this->recurring_end_date);
	}
	public function getFormatModifyRecurringMonthAttribute(){
		return Carbon::parse($this->modify_recurring_month);
	}

	public function getNumberofRepetition($start_date_recurring,$now,$no_of_repetition){
		if($start_date_recurring > $now){
			return $no_of_repetition;
		}
	}
	public function dateReccurring($count,$date,$recurring){
        //Recurring Legend
        //1 == Weekly
        //2 == Monthly
        //3 == Yearly
        if($recurring == 1){
           $date_per_occurrence = $date->addWeek($count);
           return $date_per_occurrence;
        }elseif ($recurring == 2) {
           $date_per_occurrence = $date->addMonth($count);
           return $date_per_occurrence;
        }elseif($recurring == 3){
            $date_per_occurrence =  $date->addYear($count);
           return $date_per_occurrence;
        }
    }
    public function strToLower($attribute){
		$converted_string = str_replace(' ', '-', strtolower($attribute));
		return $converted_string;
	}
}

