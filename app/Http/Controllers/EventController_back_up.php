<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use Acme\Repositories\EventRepository as Event;
use Acme\Repositories\EventDetailRepository as EventDetail;
use Acme\Repositories\OrganizationRepository as Organization;
use Acme\Repositories\VolunteerGroupRepository as VolunteerGroup;
use Acme\Repositories\ActivityLogRepository;
use App;
use Auth;
use Acme\Repositories\Cart\EventItem;
use Acme\Repositories\Cart\EasyCart;
use Gate;
use Mail;
use App\Libraries\StringConvert;
use Acme\Repositories\ParticipantRepository as Participant;
class EventController extends Controller
{
    public function __construct(Participant $participant,ActivityLogRepository $activityLog,Organization $organization,Event $event,EventDetail $event_detail, VolunteerGroup $volunteer_group){
        #$this->middleware('auth');
        $this->event = $event;
        $this->participant = $participant;
        $this->event_detail = $event_detail;
        $this->organization = $organization;
        $this->volunteer_group = $volunteer_group;
        $this->string_convert = new StringConvert();
        $this->activityLog = $activityLog;
        $this->cart = session('cart');
        #$this->auth = Auth::user();
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }
    //showing event
    public function index(Request $request, $slug)
    {
        $data['id'] = 0;
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        if(Gate::denies('view_event'))
        {
            return view('errors.errorpage');
        }
        else{
            $data['events'] = $this->event->getEvent($request,$data['organization']->id);
            $data['search'] = $request->input('search');
            $data['action'] ='';
            if($request->type =="json"){
              return $data;
            }
            return view('cocard-church.church.admin.events',$data);
        }
    }
    //form for event
    public function eventform(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // $auth = $this->activityLog->AuthGate(Gate::allows('add_event'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('add_event'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['id'] = 0;
            $data['action_name']  = 'Add';
            $data['name']  = old('name');
            $data['description']  = old('description');
            $data['capacity']  = old('capacity');
            $data['fee']  = old('fee');
            $data['start_date']  = old('start_date');
            $data['end_date']  =  old('end_date');
            $data['reminder_date']  =  old('reminder_date');
            $data['recurring']  =  '';
            $data['recurring_end_date']  =  '';
            $data['no_of_repetition']  =  '';
            $data['organization_id'] = $data['organization']->id;
            $data['action']  = route('store_event');
            $data['volunteer_groups_json'] = json_encode([]);
            if($request->type =="json"){
              return $data;
            }
            return view('cocard-church.church.admin.addevent',$data);
        }
    }
    public function getEventId(Request $request, $id)
    {
        $data['event_calendar'] =  $this->event->getEventCalendar($request, $id);
        foreach ($data['event_calendar'] as $key => $value) {
            $parent_event = $this->event->find($value->parent_event_id);
            if(isset($parent_event)){
                $newDate = Carbon\Carbon::parse( $parent_event->start_date)->format('Y-m-d');
                $data['event_calendar'][$key]->parent_event_start_date = $newDate;
            }
        }
        //dd($data['event_calendar']);
        return $data['event_calendar'];
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //create event
    public function create($slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // $auth = $this->activityLog->AuthGate(Gate::allows('add_event'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('add_event'))
        {
            return view('errors.errorpage');
        }
        else
        {

            $data = $this->event->create();
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;
           # return view('cocard-church.church.admin.events', $data);
            return view('cocard-church.church.admin.addevent',$data);
        }
    }
    //generate volunteer Group
    public function generateVolunteerGroup(Request $request, $id=null){
        // dd($request->status);
        if($request->status !== 'InActive'){
            $data = [];
            $input = $request->except("_token");
            foreach($input as $key => $item){
                $data[$key] = $item;
            }
        }
        $data['count'] = $request->count;
        $data['event'] = $this->event->find($id);
        // dd($data["count"]);
        /*if($request->type =="json"){
          return $data;
        }*/
        //$data["type"] = $request->type
        return view('cocard-church.church.admin.templates.volunteer_group_item',$data);
    }
    //saving of events/ modify event
     public function save(Request $request){
        //dd($request->all());
        if($request->id != null){
            $event_original = $this->event->findEvent($request->id);
            $s_d_original = Carbon::parse($event_original->start_date); 
        }else{
            $s_d_original =Carbon::parse($request->start_date);
        }
        $s_d = Carbon::parse($request->start_date);
        $r_e_d =Carbon::parse($request->recurring_end_date);
        

        if($request->recurring == 1){
            if(!isset($request->no_of_repetition)){
                $total_no_of_occurrence = $r_e_d->diffInWeeks($s_d); 
                $first_date =($r_e_d->diffInWeeks($s_d_original));
                $second_date = ($r_e_d->diffInWeeks($s_d));
            }else{
                $no_of_repetition_date= Carbon::parse($request->start_date)->addWeeks($request->no_of_repetition);
                $total_no_of_occurrence = $request->no_of_repetition - 1;
                $first_date = $no_of_repetition_date->diffInWeeks($s_d_original);
                $second_date = $no_of_repetition_date->diffInWeeks($s_d);
            }
        }else if($request->recurring == 2){
            if(!isset($request->no_of_repetition)){
                $total_no_of_occurrence = $r_e_d->diffInMonths($s_d); 
                $first_date = ($r_e_d->diffInMonths($s_d_original));
                $second_date = ($r_e_d->diffInMonths($s_d));
            }else{
                $no_of_repetition_date= Carbon::parse($request->start_date)->addMonths($request->no_of_repetition);
                $total_no_of_occurrence = $request->no_of_repetition - 1;
                $first_date = $no_of_repetition_date->diffInMonths($s_d_original);
                $second_date = $no_of_repetition_date->diffInMonths($s_d);
            }
            
        }else if($request->recurring == 3){
            if(!isset($request->no_of_repetition)){
                $total_no_of_occurrence = $r_e_d->diffInYears($s_d);
                $first_date =($r_e_d->diffInYears($s_d_original));
                $second_date = ($r_e_d->diffInYears($s_d));
            }else{
                $no_of_repetition_date= Carbon::parse($request->start_date)->addYears($request->no_of_repetition);
                $total_no_of_occurrence = $request->no_of_repetition - 1;
                $first_date = $no_of_repetition_date->diffInYears($s_d_original);
                $second_date = $no_of_repetition_date->diffInYears($s_d);
            }
            
        }else{
            $total_no_of_occurrence = 0;
            $first_date = 0;
            $second_date = 0;
        }
        
        $no_of_occurrence= $first_date - $second_date;

        //dd($first_date, $second_date);
        //dd($no_of_occurrence, $total_no_of_occurrence,$first_date,$second_date);
        $input = $request->except(['slug','_token','volunteer_groups','confirm']);
        $slug         = $request->slug;
        $results = $this->event->save($input, $request->id);

        if($results['status'] == false)
        {
            return redirect('/organization/'.$slug.'/administrator/events/create')->withErrors($results['results'])->withInput();
        }
        $id = $results["item"]->id;

        $volunteer_groups = (array) $request->volunteer_groups;
        $this->volunteer_group->emptyGroups($id,$volunteer_groups);//add to volunteers
        $remaining_groups = $results["item"]->volunteer_groups->toArray();
        foreach($volunteer_groups as $group){
            // dd($volunteer_groups, $group, $request->all());
            $start_date_first = Carbon::parse($group["start_date"]);
            $end_date_first = Carbon::parse($group["end_date"]);
            // dd($request->all());
           // dd($request->all(), $volunteer_groups,$group, $id);
            //dd($group["type"],$remaining_groups,"type");
            if(!$this->inArray($group["type"],$remaining_groups,"type")){
                // dd('1');
                if($id == $request->id){
                    //$group["event_id"] = $id;
                    // if($group["no_of_occurrence"] == ''){
                    //     $group["no_of_occurrence"] = $request->occurrence; 
                    // }

                    if($request->edit_as == 2){
                        // dd('1');
                       for($x= $request->occurrence; $x <=$request->total_no_of_occurrence; $x++){
                           $group["no_of_occurrence"] = $x; 
                           $group["event_id"] = $id;
                           if($request->recurring == 1){
                                if($x == 0){
                                    $group["start_date"] = $start_date_first;
                                    $group["end_date"] = $end_date_first; 
                                }else{
                                    $group["start_date"] = $start_date_first->addWeek(1);
                                    $group["end_date"] = $end_date_first->addWeek(1); 
                                }
                           }elseif($request->recurring == 2){
                                if($x == 0){
                                    $group["start_date"] = $start_date_first;
                                    $group["end_date"] = $end_date_first;
                                }else{
                                    $group["start_date"] = $start_date_first->addMonth(1);
                                    $group["end_date"] = $end_date_first->addMonth(1);
                                }
                                
                           }elseif ($request->recurring ==3) {
                                if($x == 0){           
                                    $group["start_date"] = $start_date_first;
                                    $group["end_date"] = $end_date_first;
                                }else{
                                    $group["start_date"] = $start_date_first->addYear(1);
                                    $group["end_date"] = $end_date_first->addYear(1); 
                                }
                               
                           }
                           $group["total_no_of_occurrence"] = $request->total_no_of_occurrence; 
                           $this->volunteer_group->save($group); 
                        } 
                    }else{
                      //  dd('3');
                        dd($request->edit_as);
                        $group["event_id"] = $id;
                        // if($group["no_of_occurrence"] == ''){
                        //     $group["no_of_occurrence"] = $request->occurrence; 
                        // }
                        $group["no_of_occurrence"] = $request->occurrence; 
                        $group["total_no_of_occurrence"] = $request->total_no_of_occurrence; 
                        $this->volunteer_group->save($group);
                    }
                    
                    // $group["total_no_of_occurrence"] = $total_no_of_occurrence; 
                    //  $this->volunteer_group->save($group);
                }else{
                    // dd('4');
                    if($id == $request->id){
                        dd('7');
                        if($group["no_of_occurrence"] == $request->occurrence || $group["no_of_occurrence"] == ''){
                            dd('8');
                            for($x= $request->occurrence; $x <=$request->total_no_of_occurrence; $x++){
                               $group["no_of_occurrence"] = $x; 
                               $group["event_id"] = $id;
                               if($request->recurring == 1){
                                    if($x == 0){
                                        $group["start_date"] = $start_date_first;
                                        $group["end_date"] = $end_date_first; 
                                    }else{
                                        $group["start_date"] = $start_date_first->addWeek(1);
                                        $group["end_date"] = $end_date_first->addWeek(1); 
                                    }
                               }elseif($request->recurring == 2){
                                    if($x == 0){
                                        $group["start_date"] = $start_date_first;
                                        $group["end_date"] = $end_date_first;
                                    }else{
                                        $group["start_date"] = $start_date_first->addMonth(1);
                                        $group["end_date"] = $end_date_first->addMonth(1);
                                    }
                                    
                               }elseif ($request->recurring ==3) {
                                    if($x == 0){           
                                        $group["start_date"] = $start_date_first;
                                        $group["end_date"] = $end_date_first;
                                    }else{
                                        $group["start_date"] = $start_date_first->addYear(1);
                                        $group["end_date"] = $end_date_first->addYear(1); 
                                    }
                                   
                               }
                               $group["total_no_of_occurrence"] = $request->total_no_of_occurrence; 
                               $this->volunteer_group->save($group); 
                            }
                        }  
                    }else{
                       // dd('5');
                        //$this->volunteer_group->save($group); 
                        $limit = $request->total_no_of_occurrence - $request->occurrence;
                        //dd($limit);
                       // dd($request->all(), count($volunteer_groups), count($group));
                        if($request->edit_as == 2){
                            // dd('h');
                            for($x= 0; $x <=$limit; $x++){
                                   $group["no_of_occurrence"] = $x; 
                                   $group["event_id"] = $id;
                                   if($request->recurring == 1){
                                        if($x == 0){
                                            $group["start_date"] = $start_date_first;
                                            $group["end_date"] = $end_date_first; 
                                        }else{
                                            $group["start_date"] = $start_date_first->addWeek(1);
                                            $group["end_date"] = $end_date_first->addWeek(1); 
                                        }
                                   }elseif($request->recurring == 2){
                                        if($x == 0){
                                            $group["start_date"] = $start_date_first;
                                            $group["end_date"] = $end_date_first;
                                        }else{
                                            $group["start_date"] = $start_date_first->addMonth(1);
                                            $group["end_date"] = $end_date_first->addMonth(1);
                                        }
                                        
                                   }elseif ($request->recurring ==3) {
                                        if($x == 0){           
                                            $group["start_date"] = $start_date_first;
                                            $group["end_date"] = $end_date_first;
                                        }else{
                                            $group["start_date"] = $start_date_first->addYear(1);
                                            $group["end_date"] = $end_date_first->addYear(1); 
                                        }
                                       
                                   }
                                   $group["total_no_of_occurrence"] = $limit; 
                                   //$this->volunteer_group->save($group); 
                                }
                        }elseif ($request->edit_as == 1) {
                             // dd('1');
                            // //dd($group);
                            // $group["event_id"] = $id;
                            // //dd($group);
                            // $this->volunteer_group->save($group);
                            //dd($group["no_of_occurrence"], $request->occurrence, $group, $request->all());
                            if($group["no_of_occurrence"] == $request->occurrence){
                               // dd('j', $group["no_of_occurrence"], $request->occurrence, $group, $request->all());
                                $group["event_id"] = $id;
                                $group["no_of_occurrence"] = 0;
                                $group["total_no_of_occurrence"] = 0;
                                $this->volunteer_group->save($group); 
                            }
                            
                            // for($x= 0; $x <= $request->total_no_of_occurrence; $x++){
                            //        $group["no_of_occurrence"] = $x; 
                            //        $group["event_id"] = $id;
                            //        if($request->recurring == 1){
                            //             if($x == 0){
                            //                 $group["start_date"] = $start_date_first;
                            //                 $group["end_date"] = $end_date_first; 
                            //             }else{
                            //                 $group["start_date"] = $start_date_first->addWeek(1);
                            //                 $group["end_date"] = $end_date_first->addWeek(1); 
                            //             }
                            //        }elseif($request->recurring == 2){
                            //             if($x == 0){
                            //                 $group["start_date"] = $start_date_first;
                            //                 $group["end_date"] = $end_date_first;
                            //             }else{
                            //                 $group["start_date"] = $start_date_first->addMonth(1);
                            //                 $group["end_date"] = $end_date_first->addMonth(1);
                            //             }
                                        
                            //        }elseif ($request->recurring ==3) {
                            //             if($x == 0){           
                            //                 $group["start_date"] = $start_date_first;
                            //                 $group["end_date"] = $end_date_first;
                            //             }else{
                            //                 $group["start_date"] = $start_date_first->addYear(1);
                            //                 $group["end_date"] = $end_date_first->addYear(1); 
                            //             }
                                       
                            //        }
                            //        $group["total_no_of_occurrence"] = $request->total_no_of_occurrence; 
                            //        $this->volunteer_group->save($group); 
                            //     }
                        }
                        
                    }

                }
                
            }else{
                if($group["id"] == null){
                    // dd('7');
                    $this->volunteer_group->save($group); 
                }else{
                    // dd('8');
                    // //dd($group);
                    // dd($no_of_occurrence);
                    $vg = $this->volunteer_group->findVolunteerGroup($group["id"]);
                    $vg->type              = $group["type"];
                    $vg->volunteers_needed = $group["volunteers_needed"];
                    $vg->note              = $group["note"];
                    $vg->event_id          = $id;
                    $vg->start_date        = (($group["start_date"] == 'Invalid date')?'0000-00-00 00:00':$group["start_date"]);
                    $vg->start_date        = $group["start_date"];
                    $vg->end_date          = $group["end_date"];
                    $vg->total_no_of_occurrence = $total_no_of_occurrence;
                    $vg->no_of_occurrence  = $no_of_occurrence;
                    if($group["no_of_occurrence"] == ''){
                        $vg->no_of_occurrence = $no_of_occurrence;
                    }else{
                        $vg->no_of_occurrence = $group["no_of_occurrence"];
                    }
                    $vg->save();
                }

            }
            // dd('2');
        }
        //check count
        if(count($volunteer_groups) == 0 && $id != 0){
            $this->volunteer_group->emptyGroups($id,$volunteer_groups);
        }

        if($request->type =="json"){
          return $data;
        }
        #dd($id);
        if($request->id > 0){
            $this->activityLog-
            >log_activity(Auth::user()->id,'Update Event','Updated an Event');
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Updated.");


        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Added Event','Created new Event');
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Added.");


        }
    }

    // public function 
    //saving of duplicate event
     public function saveDuplicate(Request $request){
        $input = $request->except(['slug','_token','volunteer_groups','confirm']);
        $slug         = $request->slug;
        $results = $this->event->saveDuplicate($input, $request->id);
        //dd($results);
        if($results['status'] == false)
        {
            #return redirect()->route('event_create')->withErrors($results['results'])->withInput();
            return redirect('/organization/'.$slug.'/administrator/events/create')->withErrors($results['results'])->withInput();
        }
        $id = $results["item"]->id;
        $volunteer_groups = (array) $request->volunteer_groups;

        $this->volunteer_group->emptyGroups($id,$volunteer_groups);//add to volunteers
        $remaining_groups = $results["item"]->volunteer_groups->toArray();
        foreach($volunteer_groups as $group){
            if(!$this->inArray($group["type"],$remaining_groups,"type")){
                $group["event_id"] = $id;
                $this->volunteer_group->save($group);
            }
        }
        //check count
        if(count($volunteer_groups) == 0 && $id != 0){
            $this->volunteer_group->emptyGroups($id,$volunteer_groups);
        }

        if($request->type =="json"){
          return $data;
        }
        #dd($id);
        if($request->id > 0){
            $this->activityLog->log_activity(Auth::user()->id,'Update Event','Updated an Event');
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Updated.");


        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Added Event','Created new Event');
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Added.");


        }
    }
    //modify event
    public function edit($slug, $id)
    {
        $data = $this->event->edit($id);
        $data['event'] = $this->event->find($id);
        $data['recurring'] = $data['event']->recurring;
        // dd($data['reminder_date']);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // $auth = $this->activityLog->AuthGate(Gate::allows('edit_event'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('edit_event'))
        {
            return view('errors.errorpage');
        }
        else
        {

           /* $data['volunteer_group_items'] = [];
            foreach($this->event->findEvent($id)->volunteer_groups as $group_key => $group){
                $group = $group->toArray();
                $group["count"] = $group_key;
                $data['volunteer_group_items'][] = view('cocard-church.church.admin.templates.volunteer_group_item',$group);
            }*/
            $data['volunteer_groups_json'] = json_encode($this->event->findEvent($id)->all_volunteer_groups);
            // dd(json_decode($data['volunteer_groups_json'], TRUE));
            return view('cocard-church.church.admin.addevent', $data);
        }
    }
    //duplicate event
    public function duplicate($slug, $id)
    {
        $data = $this->event->duplicate($id);
        $data['event'] = $this->event->find($id);
        $data['recurring'] = $data['event']->recurring;
        // dd($data['reminder_date']);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // $auth = $this->activityLog->AuthGate(Gate::allows('edit_event'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('edit_event'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['volunteer_groups_json'] = json_encode($this->event->findEvent($id)->all_volunteer_groups);
            return view('cocard-church.church.admin.addevent', $data);
        }
    }
    public function editAdmin($slug, $id, $date)
    {
        // dd($date);
        $data = $this->event->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $auth = $this->activityLog->AuthGate(Gate::allows('edit_event'),Auth::user()->organization_id,$data['organization']->id);

        if($auth == false)
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['volunteer_groups_json'] = json_encode($this->event->findEvent($id)->all_volunteer_groups);
            return view('cocard-church.church.admin.addevent', $data);
        }
    }

    public function modal(Request $request, $slug){
        if($request->id){
            $data['event']          = $this->event->findEvent($request->id);
            $data['start_date']     =$data['event']->format_start_date->format("m/d/Y h:i A");
            $data['end_date']       =$data['event']->format_end_date->format("m/d/Y h:i A");
            $data['slug']           = $slug;
            if($request->type =="json"){
              return $data;
            }
            return view("cocard-church.event.templates.modal",$data);
        }
        
    }
    public function add_event_modal(Request $request, $slug){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['id'] = 0;
        $data['action_name']  = 'Add';
        $data['name']  = old('name');
        $data['description']  = old('description');
        $data['capacity']  = old('capacity');
        $data['fee']  = old('fee');
        $data['start_date']  = old('start_date');
        $data['end_date']  =  old('end_date');
        $data['reminder_date']  =  old('reminder_date');
        $data['recurring']  =  '';
        $data['recurring_end_date']  =  '';
        $data['no_of_repetition']  =  '';
        $data['organization_id'] = $data['organization']->id;
        $data['action']  = route('store_event');
        $data['volunteer_groups_json'] = json_encode([]);
        if($request->type =="json"){
          return $data;
        }
        return view("cocard-church.event.templates.addevent",$data);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function viewdetails(Request $request,$slug,$id)
    {
        #dd($request);
        //$data['events'] = $this->event->getEventDetails($request,$id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['event'] = $this->event->findEvent($id);
        if($request->type =="json"){
          return $data;
        }
        return view('cocard-church.church.admin.vieweventdetails', $data);
    }
    public function viewvolunteers(Request $request,$slug,$id)
    {
        $sample = "";
        #dd($request);
        //$data['events'] = $this->event->getEventDetails($request,$id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['event'] = $this->event->findEvent($id);
        if($request->type =="json"){
            return $data;
        }
        return view('cocard-church.church.admin.vieweventvolunteers', $data);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function update(Request $request, $id)
    {
        #dd($request->all());
        $slug       = $request->slug;
        $results = $this->event->save($request, $id);
        $data['organization'] = $this->organization->getUrl($slug);
        if($results['status'] == false)
        {
            return redirect()->route('event_edit', $id)->withErrors($results['results'])->withInput();
        }
        if($request->type =="json"){
          return $data;
        }
        return redirect('/organization/'.$slug.'/administrator/events/')->with("success","Event Successfully Updated.");;
         #return redirect()->route('event_edit', $id)->with('message', 'Successfully Update Page');
    }
    public function inactive(Request $request , $slug, $id){
        #dd($slug);
        $slug                 = $request->slug;
        $results = $this->event->inactive_event($id);
        $data['organization'] = $this->organization->getUrl($slug);
        if($results['status'] == false)
        {
             return redirect()->route('event_edit', $id)->withErrors($results['results'])->withInput();
        }
        if($request->type =="json"){
          return $data;
        }
        return redirect('/organization/'.$slug.'/administrator/events/');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)//destroy($slug,$id)
    {
        //dd($request);
        if(Gate::denies('delete_event'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $this->event->inactive_event($request);
            $this->activityLog->log_activity(Auth::user()->id,'Delete Event','Event Deleted');
            return back()->with('success','Event successfully deleted!');
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

    public function addtocart(Request $request,$id=0){
       #$cart = session('cart');
        #dd($request["item"]);
        // dd($request->all());
        $data['user']           = $this->auth;
        $slug                   = $request->slug;
        $input                  = $request->except(['_token','slug']);
        $event_name             = $this->event->find($request->event_id);
        $input['name']          = $event_name->name;
        $input['description']   = $request->description;
        $input['no_of_repetition']     = $request->no_of_repetition;
        $input['recurring_end_date']     = $request->recurring_end_date;
        $input['recurring']     = $request->recurring;
        // $input['start_date']    = date("Y-m-d h:i:s A", strtotime($event_name->start_date));
        // $input['end_date']      = date("Y-m-d h:i:s A", strtotime($event_name->end_date));
        $input['fee']           = $request->fee;
        // $input['start_date']    = $request->start_date_timezone;
        $input['start_date']    = $request->start_date_timezone;
        $input['end_date']      = $request->end_date_timezone;
        $input['id']             = $this->cart->generateTransctionID(15);
        #dd($input);
        $this->cart->addItem(new EventItem($input),'event');
        $this->event->save_to_pending($request);
        $data['organization']   = $this->organization->getOrganization($request);
        $cart                   = $this->cart->getItems();
        $data['cart']           =$cart;
        $data['slug']           = $request->slug;

        $data['total']          = 0.00;
        if($request->type =="json"){
          return $data;
        }
        #return redirect('/organization/'.$slug.'/events-list')->with('message', 'You have successfully added it to cart!');
        return back()->with('message', 'You have successfully added an item to your cart!');
    }

    public function sendEmailToParticipants(Request $request,$slug)
    {
        foreach($request->email as $participant)
        {
            Mail::send('cocard-church.email.message',['participant' => $participant,'request' => $request], function ($m) use ($participant, $request) {
                    $m->to($participant, $participant)->subject($request->subject);
            });
        }
        return back()->with('message', 'Message Successfully Sent!');
    }
    // public function sendEmailReminderToParticipants(Request $request,$slug)
    // {
    //     foreach($request->email as $participant)
    //     {
    //         Mail::send('cocard-church.email.message',['participant' => $participant,'request' => $request], function ($m) use ($participant, $request) {
    //                 $m->to($participant, $participant)->subject($request->subject);
    //         });
    //     }
    //     return back()->with('message', 'Message Successfully Sent!');
    // }

}
