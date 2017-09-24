<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Volunteer;
use Carbon\Carbon;

class VolunteerGroup extends Model
{
    protected $table    = 'volunteer_groups';
    protected $fillable = ['event_id','type','note','volunteers_needed','status','start_date', 'end_date','no_of_occurrence',
'total_no_of_occurrence'];

	public function Volunteers(){
		return $this->hasMany(Volunteer::class);
	}

	public function getVolunteersApprovedAttribute(){
		$approved = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Approved')->get();
		return count($approved);
	}
	public function getApprovedVolunteersAttribute(){
		return $this->hasMany(Volunteer::class)->where('volunteer_group_status','Approved')->get();
	}
	public function getAvailableSlotsAttribute(){
		return $this->volunteers_needed - count($this->Volunteers()->where('volunteer_group_status','Approved')->get());
	}

	public function Event(){
		return $this->belongsTo(Event::class);
	}
	
	public function QueuedMessageModel(){
		return $this->hasMany(QueuedMessageModel::class);
	}

	public function getPendingAttribute(){
		 $volunteers = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Pending')->get();
		 return count($volunteers);
	}
	public function AllVolunteerPending($type){
		 $volunteers = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Pending')->get();
		 return $volunteers;
	}

	public function getCheckAttribute(){
		return 'true';
	}
	public function groupByField($field,$organization_id,$request){
		$now = Carbon::now();
		$volunteers = $this->select("v.*",
					\DB::raw('(SELECT SUM(volunteers_needed) FROM volunteer_groups as v2 
                                                 WHERE v2.status="Active" 
                                                 AND v2.event_id = v.event_id
                                                 AND v2.start_date > NOW()
                                                 AND v2.type = v.type
            		 ) as required'),
					 "e.name",
					 "e.start_date as event_start_date",
					 "e.end_date as event_end_date"
					)
					->from("volunteer_groups as v")
					->leftjoin('event as e', 'e.id', '=', 'v.event_id')
					->whereIn('event_id', function($query) use ($organization_id,$request){
					if(isset($request->user)){
						$now = Carbon::now();
						$date =Carbon::now();
						$event = $query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->where('end_date','>=',$now)->orWhereIn('id',function($query) use($now,$organization_id){
							$query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->where('recurring_end_date', '>=', $now);
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
					}else{
							$query->select('id')->from('event')->where('organization_id',$organization_id)->where('status','Active')->get();
						}
					})
					->groupBy('event_id')->where('e.status','Active');

					// ->groupBy('type','event_id')->where('v.status','Active');

		//dd($this->groupBy('type')->get());
		return $volunteers;
	}

	// public function groupByEvent($organization_id,$request){
	// 	return $this->where('organization_id',$organization_id)->where('status','Active')->get();
	// }
	// public function groupByEvent(){
	// 	return $this->groupBy('event_id')->where('status','Active')->get();
	// }
	public function allVolunteersNeeded($group){
		$now = Carbon::now();
		$volunteers_needed = $this->where('event_id',$group->event->id)->where('status','Active')->where('start_date','>',$now)->where('type',$group->type)->sum('volunteers_needed');
		return $volunteers_needed;
	}
	public function allVolunteerGroupsNeeded($event,$volunteer_group){
		$now = Carbon::now();
		$volunteers_needed = $this->where('event_id',$event->id)->where('status','Active')->where('start_date','>',$now)->where('type',$volunteer_group->type)->sum('volunteers_needed');
		return $volunteers_needed;
	}

	public function allVolunteersApproved($event_id,$type){
		// $approved = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Approved')->get();
		$approved = $this->where('event_id',$event_id)->where('type',$type)->where('volunteer_group_status','Approved')->get();
		return count($approved);
	}

	public function getStatusVolunteersNeededAttribute(){
		$now = Carbon::now();
		if($this->start_date < $now){
			return 'done';
		}else{
			return '';
		}
	}
	// public function recurring_end_date(){
	// 	if($this->Event->no_of_repetition == 0){
	// 		// $this///
	// 	}
	// }

	public function strToLower($attribute){
		$converted_string = str_replace(' ', '-', strtolower($attribute));
		return $converted_string;
	}

	public function getNumberofRepetition($start_date_recurring,$now,$no_of_repetition){
		if($start_date_recurring >= $now){
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
}