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
use App\VolunteerGroup as VolunteerGroups;
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
    public function eventform(Request $request, $slug, $instance = 0)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // Instance number of occurence for the Event
        $data['instance'] = $instance;

        $on_instance = ($instance - 1);

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
            $data['original_recurring_end_date']  =  '';
            $data['no_of_repetition']  =  '';
            $data['original_no_of_repetition']  =  '';
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

    public function increment_date_by($date, $recurring) {
        if ($recurring == 1) {
            return $date->addWeek(1);
        }
        elseif ($recurring == 2) {
            return $date->addMonth(1);
        }
        elseif ($recurring == 3) {
            return $date->addYear(1);
        }
    }

    //fetching the occurence of the event from blade
    public function getOccurence(Request $request) {
        //dd($request->occurences);
        $input = $request->except('_token');
        $event = $this->event->find($input['event_id']);
        $total = 0;
        foreach($event->Participant as $participant){//foreach participants in this event
            if($participant->occurence == $input['occurences']){
                $total += $participant->qty;//sum of all event on this occurence
            }
        }
        return $total;
    }


    //saving of events/ modify event
     public function save(Request $request){
        if($request->cb_recurring == 0){//check if event is recurring based on checkbox name "cb_recurring"
           $request->recurring = 0;
        }
        $occurrence = $this->event->occurrence($request);
        $no_of_occurrence= $occurrence['no_of_occurrence'];
        $total_no_of_occurrence = $occurrence['total_no_of_occurrence'];
        
        $input = $request->except(['slug','_token','volunteer_groups','confirm']);
        $slug         = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);

        $v_groups = (array) $request->volunteer_groups;
        $instance = $request->input('instance');

        $results = $this->event->save($input, $request->id,$v_groups,$instance);

        $start_date_hash = $this->parseFormat($request->start_date,'Y-m-d');
        $end_date_hash = $this->parseFormat($request->end_date,'Y-m-d');
        if($results['status'] == false)
        {
            if($results['id_e'] > 0){

            return redirect('/organization/'.$slug.'/administrator/events/edit/'.$results['id_e'].'/instance/'. $instance .'#'.$start_date_hash.'/'.$end_date_hash.'/'.$instance)->withErrors($results['results'])->withInput();
            }
            return redirect('/organization/'.$slug.'/administrator/events/create')->withErrors($results['results'])->withInput();
        }
        $id = $results["item"]->id;
        
        $occur_instance = ($request->input('instance') > 0) ? $request->input('instance') - 1 : 0;
        //save Volunteer Group if available
        if(count($request->volunteer_groups) > 0){
            $this->volunteer_group->saveVolunteerGroup($occur_instance,$id,$request,$total_no_of_occurrence);
        }
        if($request->type =="json"){
          return $data;
        }
        if($request->id > 0){
            $this->activityLog->log_activity(Auth::user()->id,'Update Event','Updated an Event', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Updated.");


        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Added Event','Created new Event', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Added.");


        }
    }
    
    //saving of duplicate event
     public function saveDuplicate(Request $request){
        $input = $request->except(['slug','_token','volunteer_groups','confirm']);
        $slug         = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);

        $occurrence = $this->event->occurrence($request);
        $no_of_occurrence= $occurrence['no_of_occurrence'];
        $total_no_of_occurrence = $occurrence['total_no_of_occurrence'];

        $results = $this->event->saveDuplicate($input, $request->id);
        //dd($results);
        if($results['status'] == false)
        {
            //dd($results);
            #return redirect()->route('event_create')->withErrors($results['results'])->withInput();
            return redirect('/organization/'.$slug.'/administrator/events/duplicate/'.$results['id'].'/')->withErrors($results['results'])->withInput();
        }
        $id = $results["item"]->id;
        $occur_instance = ($request->input('instance') > 0) ? $request->input('instance') - 1 : 0;
        $volunteer_groups = (array) $request->volunteer_groups;
        //dd($volunteer_groups[$occur_instance]);
        if(count($request->volunteer_groups) > 0){
            $request->duplicate = 'true';
            $this->volunteer_group->saveVolunteerGroup($occur_instance,$id,$request,$total_no_of_occurrence);
        }
        // $this->volunteer_group->emptyGroups($id,$volunteer_groups);//add to volunteers
        // $remaining_groups = $results["item"]->volunteer_groups->toArray();
        // foreach($volunteer_groups as $group){
        //     if(!$this->inArray($group["type"],$remaining_groups,"type")){
        //         $group["event_id"] = $id;
        //         $group["no_of_occurrence"] = 0;
        //         $this->volunteer_group->save($group);
        //     }
        // }
        //check count
        if(count($volunteer_groups) == 0 && $id != 0){
            $this->volunteer_group->emptyGroups($id,$volunteer_groups);
        }

        if($request->type =="json"){
          return $data;
        }
        #dd($id);
        if($request->id > 0){
            $this->activityLog->log_activity(Auth::user()->id,'Update Event','Updated an Event', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Updated.");


        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Added Event','Created new Event', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/events')->with("success","Event Successfully Added.");


        }
    }
    //modify event
    public function edit($slug, $id, $instance = 0)
    {
        $data = $this->event->edit($id);
        $data['event'] = $this->event->find($id);
        $data['recurring'] = $data['event']->recurring;
        // dd($data['reminder_date']);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // $auth = $this->activityLog->AuthGate(Gate::allows('edit_event'),Auth::user()->organization_id,$data['organization']->id);

        // Instance number of occurence for the Event
        $data['instance'] = $instance;

        $on_instance = ($instance - 1);
        $data['volunteer_groups'] = $this->volunteer_group->vg_per_occurrence($on_instance,$id);
        // dd($data);
        // exit;

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
            // $data['volunteer_groups_json'] = json_encode($this->event->findEvent($id)->all_volunteer_groups);
             $data['volunteer_groups_json'] = json_encode($this->volunteer_group->vg_per_occurrence($on_instance,$id));
            return view('cocard-church.church.admin.addevent', $data);
        }
    }
    //duplicate event
    public function duplicate($slug, $id, $instance = 0)
    {
        $data = $this->event->duplicate($id);
        $data['event'] = $this->event->find($id);
        $data['recurring'] = $data['event']->recurring;
        // dd($data['reminder_date']);
        // Instance number of occurence for the Event
        $data['instance'] = $instance;
        $on_instance = ($instance - 1);
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
            //$data['volunteer_groups_json'] = json_encode($this->event->findEvent($id)->all_volunteer_groups);
            $data['volunteer_groups_json'] = json_encode($this->volunteer_group->vg_per_occurrence($on_instance,$id));
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
        $data['original_recurring_end_date']  =  '';
        $data['original_no_of_repetition']  =  '';
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

    public function destroy(Request $request, $slug)//destroy($slug,$id)
    {
        //dd($request);
        if(Gate::denies('delete_event'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['organization'] = $this->organization->getUrl($slug);
            $this->event->inactive_event($request);
            $this->activityLog->log_activity(Auth::user()->id,'Delete Event','Event Deleted', $data['organization']->id);
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
        //dd($request);
        // dd($request->all());
        $data['user']               = $this->auth;
        
        //dd($input);
        //check event participation if all events in the series or this event only
        if($request->btn_cart == 0){//this event only
            $slug                       = $request->slug;
            $input                      = $request->except(['_token','slug']);
            $event_                     = $this->event->find($request->event_id);
            $input['name']              = $event_->name;
            $input['participant_name']  = $request->name;
            $input['description']       = $request->description;
            $input['no_of_repetition']  = $request->no_of_repetition;
            $input['recurring_end_date']= $request->recurring_end_date;
            $input['recurring']         = $request->recurring;
            // $input['start_date']    = date("Y-m-d h:i:s A", strtotime($event_name->start_date));
            // $input['end_date']      = date("Y-m-d h:i:s A", strtotime($event_name->end_date));
            $input['fee']               = $request->fee;
            // $input['start_date']    = $request->start_date_timezone;
            $input['start_date']        = $request->start_date_timezone;
            $input['end_date']          = $request->end_date_timezone;
            $input['occurence']         = $request->occur_count;
            $input['id']                = $this->cart->generateTransctionID(15);
            $this->cart->addItem(new EventItem($input),'event');
        }else{//this and future events
            $t = $request->no_of_repetition;
            $i = $request->no_of_repetition;//instance
            $e = Carbon::parse($request->recurring_end_date)->format('n/j/Y');//end date of recurring
            $count = 1;

            if($request->no_of_repetition == 0){
                switch($request->recurring){
                    case 1://weekly
                    //get the date difference 
                        $t = Carbon::parse($request->recurring_end_date)->diffInWeeks(Carbon::parse($request->start_date_timezone));
                        if($t > 52){
                            $t  = 52; //limit an occurrence to 52 or 1 year
                        }
                    break;
                    case 2://monthly
                    //get the date difference 
                        $t = Carbon::parse($request->recurring_end_date)->diffInMonths(Carbon::parse($request->start_date_timezone));
                        if($t > 12){
                            $t  = 12; //limit an occurrence to 12 or 1 year
                        }
                    break;
                    case 3://yearly
                    //get the date difference 
                        $t = Carbon::parse($request->recurring_end_date)->diffInYears(Carbon::parse($request->start_date_timezone));
                        if($t > 10){
                            $t  = 10; //limit an occurrence to 10 years
                        }
                    break;
                }
            }            
            $times = $t;
            $i = 0;
            for ($i; $i < $times ; $i++) { 
                $slug                       = $request->slug;
                $input                      = $request->except(['_token','slug']);
                $event_                     = $this->event->find($request->event_id);
                $input['name']              = $event_->name;
                $input['participant_name']  = $request->name;
                $input['description']       = $request->description;
                $input['no_of_repetition']  = $request->no_of_repetition;
                $input['recurring_end_date']= $request->recurring_end_date;
                $input['recurring']         = $request->recurring;
                $input['fee']               = $request->fee;
                if($i > 0){
                    $input['fee'] = '0.00';   
                    $input['amount'] = '0.00';   
                    $input['price'] = '0.00';   
                    $input['total'] = '0.00'; 
                    //dd($input);  
                }
                // $input['start_date']    = $request->start_date_timezone;
                switch ($input['recurring'] ) {
                    case  0:
                    break;
                    case  1:
                        $input['start_date_timezone']   = Carbon::parse($request->start_date_timezone)->addWeek($i)->format('n/j/Y');
                        $input['end_date_timezone']     = Carbon::parse($request->end_date_timezone)->addWeek($i)->format('n/j/Y');
                        $input['occurence']             = $request->occur_count;
                        $input['id']                    = $this->cart->generateTransctionID(15);                        
                        $this->cart->addItem(new EventItem($input),'event');                  
                        $this->event->save_to_pending($request);
                    break; 
                    case  2:
                        $input['start_date']        = Carbon::parse($request->start_date_timezone)->addMonth($i);
                        $input['end_date']          = Carbon::parse($request->end_date_timezone)->addMonth($i);
                        $input['occurence']         = $request->occur_count;
                        $input['id']                = $this->cart->generateTransctionID(15);
                        if($i >0){
                            $input['fee'] = '0.00';
                            dd($input, $input['fee']);
                        }
                        $this->cart->addItem(new EventItem($input),'event');  
                        $this->event->save_to_pending($request);
                    break; 
                    case  3:
                        $input['start_date']        = Carbon::parse($request->start_date_timezone)->addYear($i);
                        $input['end_date']          = Carbon::parse($request->end_date_timezone)->addYear($i);
                        $input['occurence']         = $request->occur_count;
                        $input['id']                = $this->cart->generateTransctionID(15);
                        if($i >0){
                            $input['fee'] = 0;
                        }
                        $this->cart->addItem(new EventItem($input),'event');  
                        $this->event->save_to_pending($request);
                    break;   
                }
                //dd($input['start_date']);
                //$input['start_date']        = $request->start_date_timezone;
                //$input['end_date']          = $request->end_date_timezone;
                

                
            }

        }
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

    public function parseFormat($date,$format){
        $parse_date = Carbon::parse($date)->format($format);
        return $parse_date;
    }
}
