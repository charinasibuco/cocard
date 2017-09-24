<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use App\Event;
use App\EventDetail;
use DB;
use App\ActivityLog;
use App\Organization;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class VolunteerGroupRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    use Pagination;

    protected $listener;

    public function model(){
        return 'App\VolunteerGroup';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }
    public function getOrganization($id){
         $g = $this->model->with('organization')->find($id);
         #dd($g);
        #return 'yayay';
    }

    public function store(Array $request){}

    public function edit($id){}

    public function create(){}

    public function findVolunteerGroup($id){
        return $this->model->where('id',$id)->first();
    }
    public function findEventWithVolunteerGroup($request){
        $query = $this->model->leftjoin('event', 'event.id', '=', 'volunteer_groups.event_id')
                            ->leftjoin('volunteers', 'volunteers.volunteer_group_id', '=', 'volunteer_groups.id');

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'type';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';
        return $query->select('volunteer_groups.*', 'event.*', 'volunteers.*')
            ->orderBy('volunteer_groups.'.$order_by, $sort)
            ->paginate();
    }
    public function findVolunteerGroupPerEvent($request, $id,$organization_id){
       //dd($request->all(), $id,$organization_id);
         // $query = $this->model;
        $now = Carbon::now();
         // $query2 = $this->model->whereIn('event_id',function($query) use($organization_id,$request){
         //            $query->select('id')->from('event')
         //            ->where('organization_id',$organization_id)
         //            ->where('name',$request->event_name)
         //            ->get();
         //        })
        $query2 = $this->model->where('event_id', $request->event_id)
                ->where('type',$request->volunteer_type);
        if ($request->has('search_volunteer_group')) {
            $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'type';
                $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';
            if($request->search_volunteer_group == "All"){
                $list = $query2->whereHas('event', function($query2) {
                        $query2->where('status', '=', 'Active');
                        })
                        ->where('status', '=', 'Active')
                        ->orderBy('volunteer_groups.'.$order_by, $sort)
                        ->paginate(7);
                        return $list->setPath('');

            }else{
                $search_volunteer_group = trim($request->input('search_volunteer_group'));
                // $query = $query->where(function ($query) use ($search_volunteer_group) {
                    // $query->select('volunteer_groups.*', 'event.*', 'volunteers.*')
                     // $query->where('event_id', '=', $search_volunteer_group )
                     //    ->paginate(7);
                     $list = $query2->whereHas('event', function($query2) use($search_volunteer_group, $order_by, $sort){
                        $query2->where('status', '=', 'Active');
                        })
                        ->where('status', '=', 'Active')
                        ->where('event_id', '=', $search_volunteer_group )
                        ->orderBy('volunteer_groups.'.$order_by, $sort)
                        ->paginate(7);
                    return $list->setPath('');
                // });
            }      
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'type';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';

        // return $query->select('volunteer_groups.*', 'event.*')
        //     ->orderBy('volunteer_groups.'.$order_by, $sort)
        //     ->paginate();
        $currentPage = $request->page;
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        if(isset($request->user)){
            $query2 = $query2->where('start_date','>=', $now);
        }
        $list = $query2->whereHas('event', function($query2) {
            $query2->where('status', '=', 'Active');
            })
            ->where('status', '=', 'Active')
            ->orderBy('volunteer_groups.'.$order_by, $sort)
            ->paginate(5);
            return $list->setPath('');
    }

    public function findVolunteerGroupsPerEvent($request, $id,$organization_id){
        //dd($request->all(), $id,$organization_id);
        return $this->model->where('event_id',$id)->groupBy('type')->get();
    }
    public function getUserVolunteerGroup($request,$organization_id){

        $this->SetPage($request);
        $query = $this->model;
                 
        $now = Carbon::parse()->toDateTimeString();

        // $query = $this->model
        // ->leftjoin("event",'volunteer_groups.event_id',"=","event.id")
        // ->where('volunteer_groups.start_date','>=',$now)
        // ->where("organization_id",$organization_id)
        // ->where("volunteer_groups.status","Active");
        $query = $this->model->whereIn('event_id',function($query) use($organization_id){
                    $query->select('id')->from('event')->where('organization_id',$organization_id)->get();
        });


        $order_by   = $this->SortBy;
        $sort       = $this->SortOrder;

        if ($request->has(Constants::KEYWORD)) {
            $search = trim($request->input(Constants::KEYWORD));

            $query->where('type', 'LIKE', '%' . $search . '%');
                   
        }
        
        // return $query->select('*')
        //     ->orderBy('volunteer_groups.'.$order_by, $sort)
        //     ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
        //                 Constants::PAGE_INDEX,
        //                $this->PageIndex);
         return $query->select('volunteer_groups.*')

            ->where('start_date','>=',$now)
            ->where('status','Active')
            ->orderBy('volunteer_groups.'.$order_by, $sort)
            ->paginate(7);

    }


    public function getVolunteerGroup($request,$id)
    {
        $query = $this->model->where('status','Active')->where('organization_id','=',$id);
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('event.*')
            ->orderBy('event.'.$order_by, $sort)    
            ->paginate();
    }

    public function save(Array $request){
        $this->model->create($request);  
    }
    
        public function inactive_event($id)
    {
        #dd($id);
        $event = $this->model->where('id',$id)->first();
        $event->status  = 'InActive';
        $event->save();

        return ['status' => true, 'results' => 'Success'];
    }
    public function show($id){
        return $this->model->find($id);
    }


    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }

    public function emptyGroups($event_id,$exceptions = []){
        foreach($this->model->where('event_id',$event_id)->get() as $group){
            if(!$this->inArray($group->type,$exceptions,"type")){
                // $group->delete();
                $group->status = 'InActive';
                $group->save();
            }
        }
    }

    public function inArray($value,$array,$attribute = null){
        $count = 0;
        foreach($array as $item){
            if($attribute == null){
                foreach($item as $key => $sub_item){
                    if($value == $sub_item){
                        $count++;
                    }
                }
            }else{
                if($item[$attribute] == $value){
                    $count++;
                }
            }

        }

        if($count > 0){
            return true;
        }
        return false;
    }

    public function getUniqueTypes(){
        $vg = $this->model->where('status','Active')->get();
        return array_unique($vg->pluck('type')->toArray());
    }

    public function changeStatus($id,$status){
        $vg = $this->model->where('id',$id)->first();

        $change_status_to = ($status=='Active')?'InActive':'Active';

        $vg->status = $change_status_to;
        $vg->save();

        return $change_status_to;
    }

    public function groupByField($field,$organization_id,$request){
      $group_by_field = $this->model->groupByField($field,$organization_id,$request)->orderBy('start_date','asc')->paginate(7);
      //dd($group_by_field);
      return $group_by_field->setPath('');
    }

    // public function groupByEvent(){
    //   $group_by_field = $this->model->groupByEvent();
    //   return $group_by_field;
    // }

    public function groupByFieldAPI($field,$organization_id,$request){

      $now = Carbon::now();
	  $date =Carbon::now();

       return $group_by_field = $this->model
        ->select("v.*" 
        ,"e.recurring_end_date as event_end_date"
        ,"e.start_date as event_start_date"
        ,"e.name as event_name"
        ,"e.description"
        ,\DB::raw('(SELECT SUM(volunteers_needed) FROM volunteer_groups as v2 
                                                 WHERE v2.status="Active" 
                                                 AND v2.event_id = v.event_id
                                                 AND v2.start_date > NOW()
                                                 AND v2.type = v.type
             ) as required')
        ,
        \DB::raw('(SELECT COUNT(v3.id) FROM volunteers as v3 
                                                 WHERE v3.volunteer_group_status="Pending" 
                                                 AND v3.volunteer_group_id IN
                                                 (
                                                        SELECT (v4.id) FROM volunteer_groups as v4
                                                        WHERE v4.event_id = v.event_id
                                                        AND v4.start_date > NOW()
                                                 )
             ) as pending_request')
        )
        ->leftjoin('event as e', 'e.id', '=', 'v.event_id')
        ->where("e.organization_id",$organization_id)
        ->where('v.status','Active')
        ->where('e.recurring_end_date', '>=', $now)
        ->from("volunteer_groups as v")
        ->groupBy($field)
        ->orderBy('start_date','asc')
        ->paginate(7);
    }

    public function saveVolunteerGroup($occur_instance,$id,$request,$total_no_of_occurrence){
        $vg_array = $arr_new_vgroup = $old_type = $only_new_vg = $only_old_vg = [];
        $v_groups = (array) $request->volunteer_groups;
        ksort($v_groups);
        foreach ($v_groups as $key => $v_group) {
            $start_date_first = Carbon::parse($v_group['start_date']);
            $end_date_first = Carbon::parse($v_group['end_date']);
            $recurring = $request->recurring;
            if (empty($v_group['id'])) {
                // The Newly Added Volunteer Group
                $new_group = array(
                                    'id' => null,
                                    'status' => 'Active',
                                    'no_of_occurrence' => $occur_instance,
                                    'type' => $v_group['type'],
                                    'start_date' => $v_group['start_date'],
                                    'end_date' => $v_group['end_date'],
                                    'volunteers_needed' => $v_group['volunteers_needed'],
                                    'note' => $v_group['note'],
                                    'event_id' =>$id
                                );

                // Add to the Array of Volunteer Groups
                $vg_array[$occur_instance][$v_group['type']] = $new_group;

                // Only the newly added Volunteer Group
                $only_new_vg[] = $new_group;

                // Will determine that there are newly Added Volunteer Group(s)
                $arr_new_vgroup[$v_group['type']] = $new_group;

                $this->createNewVoluteerGroup($total_no_of_occurrence+$occur_instance,$new_group, $recurring, $start_date_first, $end_date_first, $id,$occur_instance,$request);
                
                // echo '<pre>'; print_r($new_group);echo '</pre>';
                // dd($new_group);
                // exit;
            }
            else {
                if ($occur_instance == $v_group['no_of_occurrence']) {
                    $from_vgroup = $v_group;
                }

                if(isset($from_vgroup)){
                    if ($occur_instance <= $v_group['no_of_occurrence']) {
                    // Update This and Future Event
                    $vg_array[$v_group['no_of_occurrence']][$from_vgroup['type']] = array(
                            'id' => $from_vgroup['id'],
                            'status' => $from_vgroup['status'],
                            'no_of_occurrence' => $v_group['no_of_occurrence'],
                            'type' => $from_vgroup['type'],
                             'event_id' => $id,
                            // Needs to update with Carbon Date Incrementing to Weekly, Monthly and Yearly
                            // Use Carbon to increment by $v_group['no_of_occurrence']
                            'start_date' => $v_group['start_date'], // increment_date_by($start_date, $reccurring)
                            'end_date' => $v_group['end_date'], // increment_date_by($end_date, $reccurring)

                            'volunteers_needed' => $from_vgroup['volunteers_needed'],
                            'note' => $from_vgroup['note']
                        );
                    $old_type[$v_group['no_of_occurrence']] = $from_vgroup['type'];
                    }
                    else {
                        $vg_array[$v_group['no_of_occurrence']][$v_group['type']] = $v_group;
                        $old_type[$v_group['no_of_occurrence']] = $v_group['type'];   
                    }
                }

            }
        }
        //all Volunteer Groups edit as Single or Recurring 
        if(isset($from_vgroup)){
            if(isset($vg_array[$occur_instance][$from_vgroup['type']]['id'])){
                $this->updateVolunteerGroup($vg_array[$occur_instance],$occur_instance,$id,$request->edit_as,$recurring,$request->id,$request);
            }
        }
    }

    public function createNewVoluteerGroup($total_no_of_occurrence,$new_group, $recurring, $start_date_first, $end_date_first, $id,$occur_instance,$request){
        //dd($request->duplicate);
        if(isset($request->duplicate)){
            // dd('1');
            // dd($new_group);
            $new_group["no_of_occurrence"] = 0; 
            $new_group["event_id"] = $id;
            $new_group["status"] = 'Active';
            $new_group["start_date"] = $start_date_first;
            $new_group["end_date"] = $end_date_first;
            $new_group["total_no_of_occurrence"] = $total_no_of_occurrence; 
            $this->save($new_group); 
        }
        // //dd($total_no_of_occurrence,$new_group, $recurring, $start_date_first, $end_date_first, $id,$occur_instance, $edit_as);
        else{
                if($request->edit_as == 2 || $request->edit_as == 0){
                    // dd('2');
                    if(isset($request->singleToRecurring)){
                        $occur_instance=1;
                    }
                        for($x= $occur_instance; $x <= $total_no_of_occurrence; $x++){
                           $new_group["no_of_occurrence"] = $x; 
                           $new_group["event_id"] = $id;
                           $new_group["status"] = 'Active';
                           if($recurring == 1){
                                if($x == $occur_instance){
                                    $new_group["start_date"] = $start_date_first;
                                    $new_group["end_date"] = $end_date_first; 
                                }else{
                                    $new_group["start_date"] = $start_date_first->addWeek(1);
                                    $new_group["end_date"] = $end_date_first->addWeek(1); 
                                }
                           }elseif($recurring == 2){
                                if($x == $occur_instance){
                                    $new_group["start_date"] = $start_date_first;
                                    $new_group["end_date"] = $end_date_first;
                                }else{
                                    $new_group["start_date"] = $start_date_first->addMonth(1);
                                    $new_group["end_date"] = $end_date_first->addMonth(1);
                                }
                                
                           }elseif ($recurring ==3) {
                                if($x == $occur_instance){           
                                    $new_group["start_date"] = $start_date_first;
                                    $new_group["end_date"] = $end_date_first;
                                }else{
                                    $new_group["start_date"] = $start_date_first->addYear(1);
                                    $new_group["end_date"] = $end_date_first->addYear(1); 
                                }
                               
                           }
                           $new_group["total_no_of_occurrence"] = $total_no_of_occurrence; 
                           $this->save($new_group); 
                        }
            }elseif ($request->edit_as == 1) {
                 // dd('3');
                if(isset($request->duplicate)){
                   $occur_instance = 0;
                }else{
                    $occur_instance = $occur_instance;
                }
                $new_group["no_of_occurrence"] = $occur_instance; 
                $new_group["event_id"] = $id;
                $new_group["status"] = 'Active';
                $new_group["start_date"] = $start_date_first;
                $new_group["end_date"] = $end_date_first;
                $new_group["total_no_of_occurrence"] = $total_no_of_occurrence; 
                $this->save($new_group); 
            }
        }
        
        
    }
    public function deleteVGToFutureEvents($id,$no_of_occurrence,$event_id){
        $vg = $this->model->where('id',$id)->first();
        $recurring_vgs = $this->model->where('type',$vg->type)->where('event_id',$event_id)->where('no_of_occurrence','>',$no_of_occurrence)->get();
        foreach ($recurring_vgs as $recurring_vg) {
           $recurring_vg->status = 'InActive';
           $recurring_vg->save();
        }
        
    }
    
    public function deleteVG($id,$event_id){
        $vg = $this->model->where('id',$id)->first();
        $vg->status = 'InActive';
        $vg->save();
    }

    public function thisAndFutureEvents($type,$event_id,$occur_instance){
        $vg = $this->model->where('type',$type)->where('event_id',$event_id)->where('no_of_occurrence', '>=', $occur_instance)->get();
        return $vg;
    }

    public function updateVolunteerGroup($volunteer_groups,$occur_instance,$event_id,$edit_as,$recurring,$old_id,$request){
        if(isset($request->duplicate)){
            $duplicate_vg = $volunteer_groups;
            $this->duplicateEventVG($duplicate_vg,$event_id);
        }elseif (!isset($request->duplicate)) {
            foreach ($volunteer_groups as $key => $value) {
                if($value['id'] != null){
                    $vg = $this->model->where('id',$value['id'])->first();

                    if($edit_as == 1){
                        $this->singleVolunteerGroupUpdate($vg,$value,$event_id,$old_id,$occur_instance,$vg->no_of_occurrence,$request);
                    }
                    else if($edit_as ==2){
                        $vg_id = $this->model->where('id','>=',$value['id'])->where('type',$vg->type)->where('event_id', $old_id)->where('no_of_occurrence', '>=', $occur_instance)->get();
                        
                        foreach ($vg_id as $key => $value2) {
                            $value2->type               =$value['type'];
                            $value2->note               =$value['note'];
                            $value2->event_id           =$value['event_id'];
                            $value2->volunteers_needed  =$value['volunteers_needed'];
                            $value2->start_date         =$this->recurring_date($recurring,$value['start_date'],$value2->no_of_occurrence,$occur_instance);
                            $value2->end_date           =$this->recurring_date($recurring,$value['end_date'],$value2->no_of_occurrence,$occur_instance);
                            $value2->no_of_occurrence   =$this->no_of_occurrence($event_id,$old_id,$occur_instance,$value2->no_of_occurrence);
                            $value2->status             =$value['status'];
                            $value2->save();
                        }
                    }
                        
                }
                
            }
        }
        
    }

    public function recurring_date($recurring,$start_date,$no_of_occurrence,$occur_instance){
        $parse_date = Carbon::parse($start_date);
        if($recurring == 1){
            $parse_date->addWeek($no_of_occurrence - $occur_instance);
        }elseif($recurring == 2){
            $parse_date->addMonth($no_of_occurrence - $occur_instance);
        }elseif ($recurring == 3) {
            $parse_date->addMonth($no_of_occurrence - $occur_instance);
        }   
        return $parse_date;
    }
    public function singleVolunteerGroupUpdate($vg,$value,$event_id,$old_id,$occur_instance,$vg_occurrence,$request){
        //if editing is from single event to recurring event
        if($request->old_recurring == 0 && $request->recurring != $request->old_recurring){
            // dd($value);
            $request->edit_as = 2;
            $start_date = $this->dateRecurring(1,Carbon::parse($value['start_date']),$request->recurring);
            $end_date   = $this->dateRecurring(1,Carbon::parse($value['end_date']),$request->recurring);
            $request->singleToRecurring = 'true';

            $this->oneVG($vg,$value,$event_id,$old_id,$occur_instance,$vg_occurrence,$request);
            $this->createNewVoluteerGroup($request->total_no_of_occurrence,$value, $request->recurring, $start_date, $end_date, $event_id,$occur_instance,$request);
        }
        //if editing on volunteer group only
        else{
            $this->oneVG($vg,$value,$event_id,$old_id,$occur_instance,$vg_occurrence,$request);
        }
        
    }

    public function oneVG($vg,$value,$event_id,$old_id,$occur_instance,$vg_occurrence,$request){
            $vg->type               =$value['type'];
            $vg->note               =$value['note'];
            $vg->volunteers_needed  =$value['volunteers_needed'];
            $vg->event_id           =$event_id;
            $vg->status             =$value['status'];
            $vg->start_date         =$value['start_date'];
            $vg->end_date           =$value['end_date'];
            $vg->no_of_occurrence   =$this->no_of_occurrence($event_id,$old_id,$occur_instance,$vg_occurrence);
            $vg->save();
    }
    public function duplicateEventVG($duplicate_vg,$event_id){
        foreach ($duplicate_vg as $key => $value) {
            $vg = new $this->model;
            $vg->type               =$value['type'];
            $vg->note               =$value['note'];
            $vg->volunteers_needed  =$value['volunteers_needed'];
            $vg->event_id           =$event_id;
            $vg->status             =$value['status'];
            $vg->start_date         =$value['start_date'];
            $vg->end_date           =$value['end_date'];
            $vg->no_of_occurrence   =0;
            $vg->save();
        }
    }
    public function no_of_occurrence($new_id,$old_id,$occur_instance,$value_no_of_occurrence){
        if($new_id == $old_id){
            $value_no_of_occurrence;
        }elseif ($new_id != $old_id) {
            $value_no_of_occurrence = $value_no_of_occurrence - $occur_instance;
        }
        return $value_no_of_occurrence;
    }

    public function vg_per_occurrence($occurrence,$event_id){
        return $this->model->where('event_id',$event_id)->where('no_of_occurrence',$occurrence)->where('status','Active')->get();
    }

    public function getvolunteer_group(){
        return $this->model;
    }

    public function dateRecurring($count,$date,$recurring){
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

    public function allVolunteersApproved($event_id,$type){
		// $approved = $this->hasMany(Volunteer::class)->where('volunteer_group_status','Approved')->get();
		$approved = $this->model
                         ->join('volunteers as v', 'v.volunteer_group_id', '=', 'volunteer_groups.id') 
                         ->where('event_id',$event_id)->where('type',$type)->where('v.volunteer_group_status','Approved')->get();
		return count($approved);
	}

    public function allVolunteerGroupsNeeded($event,$volunteer_group){
		$now = Carbon::now();
		$volunteers_needed = $this->model->where('event_id',$event->id)->where('status','Active')->where('start_date','>',$now)->where('type',$volunteer_group->type)->sum('volunteers_needed');
		return $volunteers_needed;
	}
}   

