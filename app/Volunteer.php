<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Volunteer;
use Carbon\Carbon;

class Volunteer extends Model
{
    protected $table    = 'volunteers';
    protected $fillable = ['user_id','name','email', 'volunteer_group_id','status','volunteer_group_status'];

	public function User(){
	return $this->belongsTo(User::class);
	}

	public function VolunteerGroup(){
		return $this->belongsTo(VolunteerGroup::class);
	}

	public function getVolunteerGroupAttribute(){
		return $this->VolunteerGroup()->first();
	}

	public function Event(){
	return $this->belongsTo(Event::class);
	}
	public function TransactionDetail(){
	return $this->belongsToMany(TransactionDetail::class);
	}

	public function QueuedMessageModel(){
		return $this->hasMany(QueuedMessageModel::class);
	}

	public function getAllPendingAttribute(){
		//$this->volunteer
	}
	public function getVolunteersApprovedCountToDisabledAttribute(){
		if($this->volunteer_group_status == 'Approved'){
			return '';
		}elseif($this->VolunteerGroup->volunteers_needed <= $this->VolunteerGroup->getVolunteersApprovedAttribute()){
			if($this->volunteer_group_status == 'Approved'){
				return '';
			}
			if($this->volunteer_group_status == 'Pending' || $this->volunteer_group_status == 'Rejected'){
				return 'disabled';
			}
		}
	}
	// public function getPendingApplicantsAttribute(){
	// 	 // $volunteers = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Pending')->get();
	// 	 // return count($volunteers);
	// 	$volunteers = $this->whereIn('event_id',function($query){
	// 					$query->select('id')->from('event')->where('organization_id',$this->Event->organization_id)->get();
	// 				})
	// 				->where('status','Active')->get();
	// 	return $volunteers;
	// }
	public function allVolunteerPending($type){
		 $volunteers = Volunteer::where('volunteer_group_status','Pending')
		 			->whereIn('volunteer_group_id',function($query) use($type){
		 				$query->select('id')->from('volunteer_groups')->where('type',$type)->get();
		 			})->get();
		 return $volunteers;
	}

	// public function allPending($group){
	// 	$now = Carbon::now();
	// 	$pending = $this->whereIn('volunteer_group_id',function($query) use($group,$now){
 //    					$query->select('id')->from('volunteer_groups')
 //    					->where('event_id', $group->event->id)
 //    					->where('type',$group->type)
 //    					->where('start_date','>',$now)
 //    					->get();
 //    					})
 //    				->where('volunteer_group_status','Pending')
 //    				->sum('volunteer_group_status');
 //    	return $pending;
	// }
	public function allPending($collection,$table){
		$now = Carbon::now();
		$pending = $this->whereIn('volunteer_group_id',function($query) use($collection,$table,$now){
			if($table == 'event'){
				$query->select('id')->from('volunteer_groups')->where('event_id', $collection->id)->where('start_date','>',$now)->get();
			}else if($table == 'volunteer_group'){
				$query->select('id')->from('volunteer_groups')->where('event_id', $collection->event->id)->where('type',$collection->type)->where('start_date','>',$now)->get();
			}		
			
			})
		->where('volunteer_group_status','Pending')
		->sum('volunteer_group_status');
    	return $pending;
	}
	public function allVolunteersApproved($event_id,$type){
		// $approved = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Approved')->get();
		$now = Carbon::now();
		$approved = $this->whereIn('volunteer_group_id',function($query) use($event_id,$type,$now){
			$query->select('id')->from('volunteer_groups')->where('event_id',$event_id)->where('type',$type)->where('start_date','>',$now)->get();
		})
		->where('volunteer_group_status','Approved')->get();
		return count($approved);
	}
	// public function allPendingByVolunteerGroup($volunteer_groups){
	// 	$now = Carbon::now();
	// 	$pending = $this->whereIn('volunteer_group_id',function($query) use($event,$now){
 //    					$query->select('id')->from('volunteer_groups')
 //    					->where('event_id', $event->id)
 //    					->where('start_date','>',$now)
 //    					->where('type',$group->type)
 //    					->get();
 //    					})
 //    				->where('volunteer_group_status','Pending')
 //    				->sum('volunteer_group_status');
 //    	return $pending;
	// }
}