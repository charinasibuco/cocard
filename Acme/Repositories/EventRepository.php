<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Event;
use App\EventDetail;
use DB;
use App\ActivityLog;
use App\VolunteerGroup;
use App\Volunteer;
use Acme\Common\Constants as Constants;
use Acme\Common\Template\EventListCalendarTemplate as EventListCalendarTemplate;
use Acme\Common\CommonFunction as CommonFunction;
use Acme\Common\Pagination as Pagination;

class EventRepository extends Repository{

    use CommonFunction;
    use Pagination;

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

    /**/
    protected $listener;

    public function model(){
        return 'App\Event';
    }

    public function allEvents(){
        return $this->model->all();
    }

    public function setListener($listener){
        $this->listener = $listener;
    }
    public function getOrganization($id){
        $g = $this->model->with('organization')->find($id);
        #dd($g);
        #return 'yayay';
    }
    public function getEventPerOrganization($id){
        return $this->model->where('organization_id',$id)->get();
    }

    public function findEvent($id){
        return $this->model->find($id);
    }
    public function checkAppliedUser($volunteer_group_id,$user_id){
        $count = 0;
        $volunteers = Volunteer::where('volunteer_group_id',$volunteer_group_id)->get();
        foreach($volunteers as $volunteer){
            if($volunteer->user_id == $user_id){
                 $count++;
            }
        }
        // foreach($this->model->find($event_id)->volunteers as $volunteer){
        //     if($volunteer->user_id == $user_id){
        //         $count++;
        //     }
        // }
        return $count;
    }


    public function getEvent($request,$id)
    {
        #dd($request);
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

    public function getEventList($request,$id)
    {

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        #dd($request);
        $query = $this->model
        ->where('status','Active')->where('organization_id','=',$id);
    

        if($request->has('start_date'))
        {
            $query = $query->where(function ($query) use ($start_date , $end_date ) {
                $query->where('start_date', '>=', $start_date)
                      ->where('start_date', '<=', $end_date)
                      ->orWhere(DB::raw('
                        (
                            CASE WHEN (recurring <> 0 AND recurring_end_date = "0000-00-00 00:00:00") THEN
                                (CASE
                                WHEN recurring = 1 THEN  DATE_ADD(start_date, INTERVAL no_of_repetition WEEK) 
                                WHEN recurring = 2 THEN  DATE_ADD(start_date, INTERVAL no_of_repetition MONTH ) 
                                WHEN recurring = 3 THEN  DATE_ADD(start_date, INTERVAL no_of_repetition YEAR  )
                                ELSE start_date END)
                            WHEN (recurring = 0 AND recurring_end_date = "0000-00-00 00:00:00") THEN
                                start_date
                            ELSE recurring_end_date END
                        )') , '>=', $start_date);
            });
        }

        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }
        $order_by   = ($request->input(Constants::SORT_BY)) ? $request->input(Constants::SORT_BY) : 'start_date';
        $sort       = ($request->input(Constants::SORT_ORDER))? $request->input(Constants::SORT_ORDER) : 'asc';

        $query =  $query->select('event.*',DB::raw('
                        (
                            CASE WHEN (recurring <> 0 AND recurring_end_date = "0000-00-00 00:00:00") THEN
                                (CASE
                                WHEN recurring = 1 THEN  DATE_ADD(start_date, INTERVAL no_of_repetition WEEK) 
                                WHEN recurring = 2 THEN  DATE_ADD(start_date, INTERVAL no_of_repetition MONTH ) 
                                WHEN recurring = 3 THEN  DATE_ADD(start_date, INTERVAL no_of_repetition YEAR  )
                                ELSE start_date END)
                            WHEN (recurring = 0 AND recurring_end_date = "0000-00-00 00:00:00") THEN
                                start_date
                            ELSE recurring_end_date END
                        ) as base_end_date'))
        ->orderBy('event.'.$order_by, $sort)
        ->get();

        return $this->extract($query);
    }

    private function extract($data)
    {
        $list = array();

        foreach($data as $row)
        {
            $template = new EventListCalendarTemplate($row);
            array_push($list,$template);      
        }

        return $list;
    }

     public function getEventSub($parent_id)
    {
        #dd($request);
        return $this->model->where('parent_event_id',$parent_id)->get();
       
        //return $query;
    }

    public function getEventCalendar($request,$id)
    {
        #dd($request);
        $query = $this->model->where('status','Active')->where('organization_id','=',$id);
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                ->get();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('event.*')
        ->orderBy('event.'.$order_by, $sort)
        ->get();
    }

    public function getEventDetails($request,$id)
    {
        $query = $this->model->where('id',$id);
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

    public function needsVolunteers($id){
        $events = $this->model->where('organization_id',$id)->get();
        $needs_volunteers = [];
        foreach($events as $event){
            // if($event->volunteer_slots > 0 && ($event->start_date > Carbon::now()->toDateTimeString())){
            if($event->volunteer_slots > 0){
                $needs_volunteers[] = $event;
            }
        }
        return $needs_volunteers;
    }

    public function getEventParticipant($request)
    {
        $query = $this->model;
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
    public function getEventforVolunteer($request, $id)
    {
        // $query = $this->model->where('organization_id', $id)->get();
        // return $query;
       $query = $this->model->where('status','Active')->where('organization_id','=',$id);
       $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
       $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';       
       return $query->select('event.*')
       ->orderBy('event.'.$order_by, $sort)
       ->paginate();
    }


    public function filterEventsByGroup($events,$input,$display_table = false){
        $data = $events;
        if($input != ""){
            $data = [];
            foreach($events as $event){
                // dd($event->VolunteerGroups()->toArray());
                // $count = $event->VolunteerGroups()->count();
                if($this->objectInArray($input,"type",$event->volunteer_groups->toArray()) >= 0){
                    $data[] = $event;
                }
            }
        }

        if($display_table){
            $data_["events"] = $data;
            $data = view('cocard-church.volunteer.templates.events_table',$data_);
        }

        return $data;
    }


    public function objectInArray($value,$property,Array $array){
        $count = -1;
        foreach($array as $item){
            if($item[$property] == $value){
                $count++;
            }
        }
        return $count;
    }


    public function create(){
        $data['action']               = route('store_event');
        $data['action_name']          = 'Add';
        #$data['event']                =$this->model->with('organization')->find($id);
        $data['organization_id']      = old('organization_id');
        $data['id']                   = old('id');
        $data['edit_as']              = old('edit_as');
        $data['name']                 = old('name');
        $data['description']          = old('description');
        $data['capacity']             = old('capacity');
        $data['fee']                  = old('fee');
        $data['cb_recurring']         = old('cb_recurring');
        $data['recurring']            = old('recurring');
        $data['no_of_repetition']     = old('no_of_repetition');
        $data['start_date']           = old('converted_sdate');
        $data['end_date']             = old('end_date');
        $data['reminder_date']        = old('reminder_date');
        $data['status']               = old('status');
        $data['recurring_end_date']   = old('recurring_end_date');
        $data['original_recurring_end_date']   = '';

        return $data;
    }
    public function save($input, $id, $v_groups,$instance){
      // dd($input,$id, $v_groups,$instance);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $action     = ($id == 0) ? 'store_event' : 'church_event_update';
        #$action     = ($id == 0) ? 'store_event' : url('administrator/events-update/'.$id);
        $messages   = [
            'required' => 'The :attribute is required',
        ];
        if($id == 0){
            // $input      = $request->except(['_token','confirm']);
            
            // if($validator->fails()){

            //     #return $this->listener->failed($validator, $action, $id);
            //     return ['status' => false, 'results' => $validator];
            // }
            $reminder_date_check = strtotime($input['reminder_date']);
            if($reminder_date_check == false){
                $input['reminder_date'] = '0000-00-00 00:00:00';
            }
            else{
                $reminder_date= date("Y-m-d h:i:s A", strtotime($input['reminder_date']));
                 $input['reminder_date'] =Carbon::parse($reminder_date)->toDateTimeString();
            }
            $start_date= date("Y-m-d h:i:s A", strtotime($input['start_date']));
            $end_date= date("Y-m-d h:i:s A", strtotime($input['end_date']));
            
            if($input['start_date'] != 'Invalid date'){
                $input['start_date'] =Carbon::parse($input['start_date'])->toDateTimeString();
            }else{
                $input['start_date'] ='0000-00-00 00:00:00';
            }
            if ($input['end_date'] != 'Invalid date') {
                $input['end_date'] =Carbon::parse($input['end_date'])->toDateTimeString();
            }else{
                $input['end_date'] ='0000-00-00 00:00:00';
            }
            //dd($input['start_date'], $input['end_date'], $input['reminder_date']);

            if($input['cb_recurring'] == 1){
                 if($input['repeat'] == 0){
                    $input['no_of_repetition'] = 0;
                    $input['recurring_end_date']= date("Y-m-d h:i:s A", strtotime($input['recurring_end_date']));
                }else{
                    $input['recurring_end_date'] = '0000-00-00 00:00:00';
                }
            }else{
                $input['recurring'] = 0;
                $input['recurring_end_date'] = '0000-00-00 00:00:00';
                $input['no_of_repetition'] = 0;
            }
            $validator  = Validator::make($input, [
                'name'                      => 'required',
                'description'               => 'required',
                'capacity'                  => 'required',
                'fee'                       => 'required',
                'start_date2'                => 'required',
                'end_date2'                  => 'required',
                // 'reminder_date'             => 'required'
            ], $messages);
            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator, 'input' => $input];
            }
            //$item = $this->model->create($input);
            //dd($input['recurring_end_date']);
            $item = $this->model->create(['organization_id'     => $input['organization_id'],
                                        'name'                  => $input['name'],
                                        'description'           => $input['description'],
                                        'capacity'              => $input['capacity'],
                                        'fee'                   => $input['fee'],
                                        'recurring'             => $input['recurring'],
                                        'recurring_end_date'    => $input['recurring_end_date'],
                                        'original_recurring_end_date' => $input['recurring_end_date'],
                                        'no_of_repetition'      =>  (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                        'original_no_of_repetition' =>(is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                        'start_date'            => $input['start_date'],
                                        'end_date'              => $input['end_date'],
                                        'reminder_date'         => $input['reminder_date'],
                                        'status'                => 'Active'

                ]);
            $this->model->orderBy('created_at', 'name');
            
            return ['status' => true, 'results' => 'Success', 'item' => $item, 'input' => $input];
            #$this->listener->setMessage('User is successfully created!');
        }else{
            $check_recurring = $this->model->where('id',$id)->first();
            $input['start_date'] =Carbon::parse($input['start_date'])->toDateTimeString();
                $input['end_date'] =Carbon::parse($input['end_date'])->toDateTimeString();
                $input['reminder_date'] =Carbon::parse($input['reminder_date'])->toDateTimeString();
                if($input['cb_recurring'] == 1){
                    if($input['repeat'] == 0){
                        $input['no_of_repetition'] = 0;
                        $input['recurring_end_date']= date("Y-m-d h:i:s A", strtotime($input['recurring_end_date']));
                    }else{
                        $input['recurring_end_date'] = '0000-00-00 00:00:00';
                    }
                }else{
                    $input['recurring'] = 0;
                    $input['recurring_end_date'] = '0000-00-00 00:00:00';
                    $input['no_of_repetition'] = 0;
                }
                $validator  = Validator::make($input, [
                    'name'                      => 'required',
                    'description'               => 'required',
                    'capacity'                  => 'required',
                    'fee'                       => 'required',
                    'start_date2'                => 'required',
                    'end_date2'                  => 'required',
                    // 'reminder_date'             => 'required',
                ], $messages);
                if($validator->fails()){
                    #return $this->listener->failed($validator, $action, $id);
                    
                    // $input['volunteer_groups_json'][] = json_encode($v_groups);
                    return ['status' => false, 'results' => $validator,  'input' => $input,'id_e'=> $id];
                }
            //one-time update
            if($check_recurring->recurring == 0){
                $this->model->where('id',$id)->update([
                                            'organization_id'       => $input['organization_id'],
                                            'name'                  => $input['name'],
                                            'description'           => $input['description'],
                                            'capacity'              => $input['capacity'],
                                            'fee'                   => $input['fee'],
                                            'parent_event_id'       => $input['edit_as'],
                                            'modify_recurring_month'=> $input['hash'],
                                            'recurring'             => $input['recurring'],
                                            'recurring_end_date'    => $input['recurring_end_date'],
                                            'no_of_repetition'      => (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                            'original_recurring_end_date' => $input['recurring_end_date'],
                                            'original_no_of_repetition' =>(is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                            'start_date'            => $input['start_date'],
                                            'end_date'              => $input['end_date'],
                                            'reminder_date'         => $input['reminder_date'],
                                            'status'                => 'Active'

                                        ]);
            }else{
                $input['start_date'] =Carbon::parse($input['start_date'])->toDateTimeString();
                $input['end_date'] =Carbon::parse($input['end_date'])->toDateTimeString();
                $input['reminder_date'] =Carbon::parse($input['reminder_date'])->toDateTimeString();
                if($input['cb_recurring'] == 1){
                    if($input['repeat'] == 0){
                        $input['no_of_repetition'] = 0;
                        $input['recurring_end_date']= date("Y-m-d h:i:s A", strtotime($input['recurring_end_date']));
                    }else{
                        $input['recurring_end_date'] = '0000-00-00 00:00:00';
                    }
                }else{
                    $input['recurring'] = 0;
                    $input['recurring_end_date'] = '0000-00-00 00:00:00';
                    $input['no_of_repetition'] = 0;
                }
                $validator  = Validator::make($input, [
                    'name'                      => 'required',
                    'description'               => 'required',
                    'capacity'                  => 'required',
                    'fee'                       => 'required',
                    'start_date2'                => 'required',
                    'end_date2'                  => 'required',
                    // 'reminder_date'             => 'required',
                ], $messages);
                if($validator->fails()){
                    #return $this->listener->failed($validator, $action, $id);
                    return ['status' => false, 'results' => $validator, 'input' => $input,'id'=> $id];
                }

                //dd($input['recurring_end_date']);
                if($input['edit_as'] == 1){
                    $getEv = $this->model->where('id',$id)->first();
                   // dd($getEv, $input);
                    $start_date_timezone_per_occurrence = Carbon::parse($input['start_date_timezone_per_occurrence'])->toDateTimeString();
                    $recurring_end_date_parse = Carbon::parse($getEv['original_recurring_end_date'])->format('Y-m-d');
                    $recurring_end_date_input_parse = Carbon::parse($input['recurring_end_date'])->format('Y-m-d'); 
                    if($input['no_of_repetition'] > 0){
                        $no_of_repetition = $input['no_of_repetition']+($instance-1);
                    }else if($input['no_of_repetition'] == 0){
                        $no_of_repetition = $input['no_of_repetition'];
                    }
                                          
                   // dd($getEv['name'], $input['name'] , $getEv['description'], $input['description'] , $start_date_timezone_per_occurrence, $input['start_date'], $getEv['recurring'], $input['recurring'], $recurring_end_date_input_parse, $recurring_end_date_parse, $getEv['original_no_of_repetition'], $no_of_repetition);
                    if($getEv['name'] == $input['name'] && $getEv['description'] == $input['description'] && $start_date_timezone_per_occurrence == $input['start_date'] && $getEv['recurring'] == $input['recurring'] && $recurring_end_date_parse == $recurring_end_date_input_parse && $getEv['original_no_of_repetition'] == $no_of_repetition)
                    {
                        if($getEv['fee'] != $input['fee']){
                           $getEv->fee = $input['fee'];
                            $getEv->save(); 
                        }
                        $item = $this->findEvent($id); 
                    }
                    else
                    {
                        $new_event = $this->model->create(['organization_id'=> $input['organization_id'],
                                                'name'                  => $input['name'],
                                                'description'           => $input['description'],
                                                'capacity'              => $input['capacity'],
                                                'fee'                   => $input['fee'],
                                                'parent_event_id'       => $getEv['id'],
                                                'modify_recurring_month'=> $input['hash'],
                                                'recurring'             => 0,
                                                'recurring_end_date'    => $input['end_date'],
                                                'no_of_repetition'      => (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                                'original_recurring_end_date'    => $input['end_date'],
                                                'original_no_of_repetition'      => (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                                'start_date'            => $input['start_date'],
                                                'end_date'              => $input['end_date'],
                                                'reminder_date'         => $input['reminder_date'],
                                                'status'                => 'Active'

                                            ]);
                            
                        $this->model->where('id',$id)->update([
                                                'modify_recurring_month'=>(','.$getEv['modify_recurring_month'].','.$input['hash'])
                                            ]);
                        $item = $this->findEvent($new_event->id);
                        // dd($item);
                        $item['id'] = $new_event->id;
                    }

                }else{
                        $getEv = $this->model->where('id',$id)->first();
                      
                        $start_date_timezone_per_occurrence = Carbon::parse($input['start_date_timezone_per_occurrence'])->toDateTimeString();
                        $recurring_end_date = Carbon::parse($input['recurring_end_date'])->toDateTimeString();
                        $recurring_end_date_parse = Carbon::parse($getEv['original_recurring_end_date'])->format('Y-m-d');
                        $recurring_end_date_input_parse = Carbon::parse($input['recurring_end_date'])->format('Y-m-d');  
                        if($input['no_of_repetition'] > 0){
                            $no_of_repetition = $input['no_of_repetition']+($instance-1);
                        }else if($input['no_of_repetition'] == 0){
                            $no_of_repetition = $input['no_of_repetition'];
                        }
                       //dd($getEv['name'] == $input['name'], $getEv['description'], $input['description'], $start_date_timezone_per_occurrence == $input['start_date'], $getEv['recurring'], $input['recurring'], $recurring_end_date_parse, $recurring_end_date_input_parse, $getEv['original_no_of_repetition'], ($input['no_of_repetition']+$getEv['no_of_repetition']), $getEv['recurring'], $input['recurring'],$input['no_of_repetition'],$getEv['no_of_repetition']);
                        if($getEv['name'] == $input['name'] && $getEv['description'] == $input['description'] && $start_date_timezone_per_occurrence == $input['start_date'] && $getEv['recurring'] == $input['recurring'] && $recurring_end_date_parse == $recurring_end_date_input_parse && $getEv['original_no_of_repetition'] == $no_of_repetition && $getEv['recurring'] == $input['recurring'])
                        {
                            //dd('dont create new', $getEv['name'] , $input['name'] , $getEv['description'] , $input['description'] , $start_date_timezone_per_occurrence, $input['start_date'] , $getEv['recurring'], $input['recurring'] , $getEv['recurring_end_date'] , $recurring_end_date , $getEv['no_of_repetition'] ,$input['no_of_repetition']);
                            if($getEv['fee'] != $input['fee']){
                               $getEv->fee = $input['fee'];
                                $getEv->save(); 
                            }
                            $item = $this->findEvent($id); 
                        }else{
                            //dd('create new', $getEv['name'] , $input['name'] , $getEv['description'] , $input['description'] , $start_date_timezone_per_occurrence, $input['start_date'] , $getEv['recurring'], $input['recurring'] , $getEv['recurring_end_date'] , $recurring_end_date , $getEv['no_of_repetition'] ,$input['no_of_repetition']);
                            $new_event = $this->model->create([
                                            'organization_id'       => $input['organization_id'],
                                            'name'                  => $input['name'],
                                            'description'           => $input['description'],
                                            'capacity'              => $input['capacity'],
                                            'fee'                   => $input['fee'],
                                            'parent_event_id'       => $getEv['id'],
                                            'modify_recurring_month'=> $input['hash'],
                                            'recurring'             => $input['recurring'],
                                            'recurring_end_date'    => $getEv['recurring_end_date'],
                                            'no_of_repetition'      => (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                            'original_recurring_end_date'    => $getEv['recurring_end_date'],
                                            'original_no_of_repetition'      => (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                            'start_date'            => $input['start_date'],
                                            'end_date'              => $input['end_date'],
                                            'reminder_date'         => $input['reminder_date'],
                                            'status'                => 'Active'

                                        ]);
                            $new_date = Carbon::parse($input['hash'])->subDays(1);
                            //dd($new_date);
                            $this->model->where('id',$id)->update([
                                                'modify_recurring_month'=>($getEv['modify_recurring_month'].','.$input['hash']),
                                                'recurring_end_date'    => date("Y-m-d h:i:s A", strtotime($new_date)),
                                                'no_of_repetition'      => $getEv['no_of_repetition']-$input['no_of_repetition']
                                                ]);
                            $item = $this->findEvent($new_event->id);

                            }
                }

            }

            if($input['check_if_recurring'] == 0){
                $item = $this->findEvent($id);
            }
            #
            #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        // dd($item);
        return ['status' => true, 'results' => 'Success','item' => $item,  'input' => $input,'id'=> $id];
    }
    public function saveDuplicate($input, $id){
        //dd($input);
        //dd($input['start_date'],$input['end_date']);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $action     = ($id == 0) ? 'store_event' : 'church_event_update';
        #$action     = ($id == 0) ? 'store_event' : url('administrator/events-update/'.$id);
        $messages   = [
            'required' => 'The :attribute is required',
        ];
            $reminder_date_check = strtotime($input['reminder_date']);
            if($reminder_date_check == false){
                $input['reminder_date'] = '0000-00-00 00:00:00';
            }
            else{
                $reminder_date= date("Y-m-d h:i:s A", strtotime($input['reminder_date']));
                 $input['reminder_date'] =Carbon::parse($reminder_date)->toDateTimeString();
            }
            $start_date= date("Y-m-d h:i:s A", strtotime($input['start_date']));
            $end_date= date("Y-m-d h:i:s A", strtotime($input['end_date']));
            

            $input['start_date'] =Carbon::parse($input['start_date'])->toDateTimeString();
            $input['end_date'] =Carbon::parse($input['end_date'])->toDateTimeString();
           

            //dd($input['start_date'], $input['end_date'], $input['reminder_date']);

            if($input['cb_recurring'] == 1){
                 if($input['repeat'] == 0){
                    $input['no_of_repetition'] = 0;
                    $input['recurring_end_date']= date("Y-m-d h:i:s A", strtotime($input['recurring_end_date']));
                }else{
                    $input['recurring_end_date'] = '0000-00-00 00:00:00';
                }
            }else{
                $input['recurring'] = 0;
                $input['recurring_end_date'] = '0000-00-00 00:00:00';
                $input['no_of_repetition'] = 0;
            }
            $validator  = Validator::make($input, [
                'name'                      => 'required',
                'description'               => 'required',
                'capacity'                  => 'required',
                'fee'                       => 'required',
                'start_date'                => 'required',
                'end_date'                  => 'required',
                // 'reminder_date'             => 'required'
            ], $messages);
            //$item = $this->model->create($input);
            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator,  'input' => $input,'id' => $id];
            }
            $item = $this->model->create(['organization_id'     => $input['organization_id'],
                                        'name'                  => $input['name'],
                                        'description'           => $input['description'],
                                        'capacity'              => $input['capacity'],
                                        'fee'                   => $input['fee'],
                                        'recurring'             => $input['recurring'],
                                        'recurring_end_date'    => $input['recurring_end_date'],
                                        'no_of_repetition'      =>  (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                        'original_recurring_end_date'    => $input['recurring_end_date'],
                                        'original_no_of_repetition'      =>  (is_null($input['no_of_repetition']) ? 0 : $input['no_of_repetition']),
                                        'start_date'            => $input['start_date'],
                                        'end_date'              => $input['end_date'],
                                        'reminder_date'         => $input['reminder_date'],
                                        'status'                => 'Active'

                ]);
            $this->model->orderBy('created_at', 'name');
            
        return ['status' => true, 'results' => 'Success','item' => $item];
    }

     public function duplicate($id){
        #$data['action']                = url('administrator/events-update/'.$id);
        $data['action']                 = route('event_update_duplicate',$id);
        $data['action_name']            = 'Duplicate';
        $data['id']                     = $id;
        $data['event']                  = $this->model->with('organization')->find($id);
        $data['name']                   = (is_null(old('name'))?$data['event']->name:old('name'));
        $data['organization_id']        = (is_null(old('organization_id'))?$data['event']->organization_id:old('organization_id'));
        $data['description']            = (is_null(old('description'))?$data['event']->description:old('description'));
        $data['capacity']               = (is_null(old('capacity'))?$data['event']->capacity:old('capacity'));
        $data['fee']                    = (is_null(old('fee'))?$data['event']->fee:old('fee'));
        $data['start_date']             = (is_null(old('start_date'))?$data['event']->format_start_date->format("m/d/Y h:i A"):old('start_date'));
        $data['end_date']               = (is_null(old('end_date'))?$data['event']->format_end_date->format("m/d/Y h:i A"):old('end_date'));
        $data['recurring_end_date']     = (is_null(old('recurring_end_date'))?$data['event']->format_end_date->format("m/d/Y"):old('recurring_end_date'));
        $data['original_recurring_end_date']     =(is_null(old('original_recurring_end_date'))?$data['event']->format_original_recurring_end_date->format("m/d/Y"):old('original_recurring_end_date'));
        $data['recurring']              = (is_null(old('recurring'))?$data['event']->recurring:old('recurring'));
        $data['no_of_repetition']       = (is_null(old('no_of_repetition'))?$data['event']->no_of_repetition:old('no_of_repetition'));
        // $data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->format_reminder_date->format("m/d/Y h:i A"):old('reminder_date'));
        $data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->format_reminder_date->format("m/d/Y"):old('reminder_date'));
         if($data['event']->reminder_date == '0000-00-00 00:00:00')
        {
          $data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->reminder_date:old('reminder_date'));  
        }else{
         $data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->format_reminder_date->format("m/d/Y"):old('reminder_date'));
        }
        return $data;
    }
    public function edit($id){
        #$data['action']                = url('administrator/events-update/'.$id);
        $data['action']                 = route('event_update',$id);
        $data['action_name']            = 'Edit';
        $data['id']                     = $id;
        $data['event']                  = $this->model->with('organization')->find($id);
        $data['name']                   = (is_null(old('name'))?$data['event']->name:old('name'));
        $data['organization_id']        = (is_null(old('organization_id'))?$data['event']->organization_id:old('organization_id'));
        $data['description']            = (is_null(old('description'))?$data['event']->description:old('description'));
        $data['capacity']               = (is_null(old('capacity'))?$data['event']->capacity:old('capacity'));
        $data['fee']                    = (is_null(old('fee'))?$data['event']->fee:old('fee'));
        // $data['start_date']             = (is_null(old('start_date2'))?$data['event']->format_start_date->format("m/d/Y h:i A"):old('start_date2'));
        $data['start_date']             = $data['event']->format_start_date->format("m/d/Y h:i A");
        $data['end_date']               = (is_null(old('end_date2'))?$data['event']->format_end_date->format("m/d/Y h:i A"):old('end_date2'));
        $data['recurring_end_date']     = (is_null(old('recurring_end_date'))?$data['event']->format_recurring_end_date->format("m/d/Y"):old('recurring_end_date'));
        $data['original_recurring_end_date']     =(is_null(old('original_recurring_end_date'))?$data['event']->format_original_recurring_end_date->format("m/d/Y"):old('original_recurring_end_date'));
        $data['recurring']              = (is_null(old('recurring'))?$data['event']->recurring:old('recurring'));
        $data['no_of_repetition']       = (is_null(old('no_of_repetition'))?$data['event']->no_of_repetition:old('no_of_repetition'));
        $data['original_no_of_repetition']       = $data['event']->original_no_of_repetition;
        if($data['event']->reminder_date == '0000-00-00 00:00:00')
        {
          $data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->reminder_date:old('reminder_date'));  
        }else{
         $data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->format_reminder_date->format("m/d/Y"):old('reminder_date'));
        }
        if($data['event']->recurring_end_date == '0000-00-00 00:00:00')
        {
          $data['recurring_end_date']          = (is_null(old('recurring_end_date'))?$data['event']->recurring_end_date:old('recurring_end_date'));  
        }else{
         $data['recurring_end_date']          = (is_null(old('recurring_end_date'))?$data['event']->format_recurring_end_date->format("m/d/Y"):old('recurring_end_date'));
        }
        //$data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->format_reminder_date->format("m/d/Y h:i A"):old('reminder_date'));
        //$data['reminder_date']          = (is_null(old('reminder_date'))?$data['event']->reminder_date:old('reminder_date'));
        //dd($data);
        return $data;
    }

    /*public function update(array $request, $id){
    $this->model->find($id)->update($request);
}*/
    public function inactive_event($request)
    {
        $arr = explode("/",$request->past_date);
        //dd($request->all());
        switch ($request->delete_event) {
            case 1://this event only
                //dd('1');

                $getEv = $this->model->where('id',$request->id)->first();
                // dd($getEv->id);
                VolunteerGroup::where('event_id', $getEv->id)->where('no_of_occurrence', $request->occurrence_modal)->update(['status' => 'InActive']);
                $event = $this->model->where('id',$request->id)->update([
                'modify_recurring_month' => $getEv['modify_recurring_month'].','.$arr[0]
                ]);
            break;
            case 2://this event and future events
               // dd($request);
                $getEv = $this->model->where('id',$request->id)->first();
               //dd( $arr[1]);
                if($getEv->no_of_repetition > 0){
                    $m = $getEv['no_of_repetition']- $arr[1]+1;
                    VolunteerGroup::where('event_id', $getEv->id)->where('no_of_occurrence','>=', $request->occurrence_modal)->update(['status' => 'InActive']);
                    $event = $this->model->where('id',$request->id)->update([
                                    'no_of_repetition' =>   $getEv['no_of_repetition'] - $m
                    ]);
                }else{
                    $event = $this->model->where('id',$request->id)->update([
                                    'recurring_end_date' => $request->past_date,
                                     'modify_recurring_month' => $getEv['modify_recurring_month'].','.$arr[0]
                    ]);
                    VolunteerGroup::where('event_id', $getEv->id)->where('no_of_occurrence','>=', $request->occurrence_modal)->update(['status' => 'InActive']);
                }
            break;
            case 3://all event
                //dd($request->id);
                VolunteerGroup::where('event_id', $request->id)->update(['status' => 'InActive']);
                $event = $this->model->where('id',$request->id)->update([
                'status' => 'InActive'
                ]);
            break;
        }
        

        return ['status' => true, 'results' => 'Success'];
    }
    public function occurrence($request){
        if($request->id != null){
            $event_original = $this->findEvent($request->id);
            $s_d_original = Carbon::parse($event_original->start_date); 
        }else{
            $s_d_original =Carbon::parse($request->start_date);
        }

        $s_d = Carbon::parse($request->start_date);
        $r_e_d =Carbon::parse($request->recurring_end_date);
         if($request->recurring == 1){
                if(!isset($request->no_of_repetition)){
                    $data['total_no_of_occurrence'] = $r_e_d->diffInWeeks($s_d); 
                    $first_date =($r_e_d->diffInWeeks($s_d_original));
                    $second_date = ($r_e_d->diffInWeeks($s_d));
                }else{
                    $no_of_repetition_date= Carbon::parse($request->start_date)->addWeeks($request->no_of_repetition);
                    $data['total_no_of_occurrence'] = $request->no_of_repetition - 1;
                    $first_date = $no_of_repetition_date->diffInWeeks($s_d_original);
                    $second_date = $no_of_repetition_date->diffInWeeks($s_d);
                }
            }else if($request->recurring == 2){
                if(!isset($request->no_of_repetition)){
                    $data['total_no_of_occurrence'] = $r_e_d->diffInMonths($s_d); 
                    $first_date = ($r_e_d->diffInMonths($s_d_original));
                    $second_date = ($r_e_d->diffInMonths($s_d));
                }else{
                    $no_of_repetition_date= Carbon::parse($request->start_date)->addMonths($request->no_of_repetition);
                    $data['total_no_of_occurrence'] = $request->no_of_repetition - 1;
                    $first_date = $no_of_repetition_date->diffInMonths($s_d_original);
                    $second_date = $no_of_repetition_date->diffInMonths($s_d);
                }
                
            }else if($request->recurring == 3){
                if(!isset($request->no_of_repetition)){
                    $data['total_no_of_occurrence'] = $r_e_d->diffInYears($s_d);
                    $first_date =($r_e_d->diffInYears($s_d_original));
                    $second_date = ($r_e_d->diffInYears($s_d));
                }else{
                    $no_of_repetition_date= Carbon::parse($request->start_date)->addYears($request->no_of_repetition);
                    $data['total_no_of_occurrence'] = $request->no_of_repetition - 1;
                    $first_date = $no_of_repetition_date->diffInYears($s_d_original);
                    $second_date = $no_of_repetition_date->diffInYears($s_d);
                }
                
            }else{
                $data['total_no_of_occurrence'] = 0;
                $first_date = 0;
                $second_date = 0;
            }
            $data['no_of_occurrence']= $first_date - $second_date;

            return $data;
    }

    public function frequency($recurring,$first_date,$second_date,$operation){
        if($recurring==1){
            if($operation =='add'){
                $date = $first_date->addWeeks($second_date);
            }
            if($operation =='subtract'){
                $date = $first_date->diffInWeeks($second_date);
            }
        }
        else if($recurring==2){
            if($operation =='add'){
                $date = $first_date->addMonths($second_date);
            }
            if($operation =='subtract'){
                $date = $first_date->diffInMonths($second_date);
            }
        }else if($recurring==3){
            if($operation =='add'){
                $date = $first_date->addYears($second_date);
            }
            if($operation =='subtract'){
                $date = $first_date->diffInYears($second_date);
            }
        }
        return $date;
    }

    public function show($id){
        return $this->model->find($id);
    }


    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }
    public function save_to_pending($request){
        $event  = $this->model->where('id', $request->event_id)->first();
        $pending = $event->pending + $request->qty;
        return $this->model->where('id', $request->event_id)->update(['pending' => $pending]);
    }

    public function groupByEvent($organization_id,$request){
        $this->SetPage($request);

        $group_by_event =  $this->model->groupByEvent($organization_id,$request)
        ->orderBy('start_date','asc');

        if($request->type== "json")
        {
            $group_by_event = $group_by_event->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex);
        }
        else
        {
            $group_by_event = $group_by_event->paginate($this->PageSize);
            $group_by_event = $group_by_event->setPath("");
        }
    

        return $group_by_event;
    }
}
