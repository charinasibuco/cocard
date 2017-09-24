<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\VolunteerRepository as Volunteer;
use Acme\Repositories\OrganizationRepository as Organization;
use Acme\Repositories\EventRepository as Event;
use Acme\Repositories\UserRepository as User;
use Acme\Repositories\VolunteerGroupRepository as VolunteerGroup;
use Acme\Repositories\ActivityLogRepository;
use Acme\Repositories\QueuedMessageRepository;
use Auth;
use App;
use App\Http\Requests;
use Acme\Helper\Api;
use Gate;
use Mail;
use Carbon\Carbon;
use DateTime;
use App\VolunteerGroup as VolunteerGroups;
use Acme\Common\CommonFunction as CommonFunction;
use Acme\Common\Constants as Constants;
use Acme\Common\DataResult as DataResult;

class VolunteerController extends Controller
{
    use CommonFunction;

    protected $slug;
    public function __construct(ActivityLogRepository $activityLog,User $user,Volunteer $volunteer,Organization $organization,Event $event,VolunteerGroup $volunteer_group, QueuedMessageRepository $queued_message){
        $this->volunteer = $volunteer;
        $this->organization = $organization;
        $this->event = $event;
        $this->user = $user;
        $this->volunteer_group = $volunteer_group;
        $this->activityLog = $activityLog;
        $this->queued_message = $queued_message;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }
    public function index(Request $request, $slug){
        $data['organization'] = $this->organization->getUrl($slug);
        $organization_id = $data['organization']->id;
        $data['slug'] = $this->organization->getUrl($slug)->url;
        $this->slug = $data['slug'];
        $data['page'] = 'page='.$request->page;
        $data['id'] = '';
        //$data['events'] = $this->event->needsVolunteers();
        //$data['events_table'] = $this->event->filterEventsByRole($this->event->needsVolunteers(),"",TRUE);
        $data['volunteer_role_titles'] = $this->volunteer_group->getUniqueTypes();
        $data['volunteer_groups'] = $this->volunteer_group->getUserVolunteerGroup($request,$organization_id);

        $data['volunteer_group_by_field'] = $this->volunteer_group->groupByField('event_id',$organization_id,$request);
        $data[Constants::MODULE] = Constants::VOLUNTEER_LIST_GUEST;


        $events = $this->event->groupByEvent($organization_id,$request);
        foreach($events as $event)
        {
            $event->VolunteerGroupByType;
                
            foreach(@$event->VolunteerGroupByType as $vg)
            {
                $vg->events_schedule = $event->volunteerGroupsUnderType($vg->type);
                $vg->required_participants = $this->volunteer_group->allVolunteersApproved($event->id,$vg->type);
                $vg->total_participants_needed = $this->volunteer_group->allVolunteerGroupsNeeded($event,$vg);
            }
        }

        $data["events"] = $events;

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        return Api::displayData($data,'cocard-church.volunteer.index',$request);
    }

    public function admin(Request $request, $slug){
       // dd($request->all());
        $data['organization']           = $this->organization->getUrl($slug);
        $organization_id = $data['organization']->id;
        // $auth = $this->activityLog->AuthGate(Gate::allows('view_volunteer'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('view_volunteer'))
        {
            return view('errors.errorpage');
        }
        else
        {
            // dd($request);
            $data['slug']                   = $this->organization->getUrl($slug)->url;
            #$data['event'] = $this->event->findEvent($request->event_id);
            // $data['events']                 = $this->event->getEventforVolunteer($request, $data['organization']->id);
            $data['events']                 = $this->event->needsVolunteers($organization_id);
            // dd($data['events']);
            $data['volunteers']             = $this->volunteer->getVolunteersAll($request);
            $data['volunteer_group_with_event'] = $this->volunteer_group->findEventWithVolunteerGroup($request);
            #dd($data['volunteers']);
            $data['prev_event']             = '';

            foreach($data['volunteers'] as $volunteer){
                #dd($volunteer);
                $data['volunteer_group']    = $this->volunteer_group->findVolunteerGroup($volunteer->volunteer_group_id);
                $data['event_title']        = $this->event->findEvent($volunteer->volunteer_group_id);
                $data['user_name']          = $this->user->findUser($volunteer->user_id);
                $data['event_date']         = $this->event->findEvent($volunteer->volunteer_group_id);
            }
            $data['search_volunteer_group'] = $request->input('search_volunteer_group');
            $data['volunteer_groups']       =  $this->volunteer_group->findVolunteerGroupPerEvent($request,$request->search_volunteer_group,$organization_id);
            // foreach($data['volunteer_groups'] as $q){
            //     #dd($this->volunteer->findVolunteerPerGroup($q->id));
            // $data['volunteers']             =  $this->volunteer->findVolunteerPerGroup($q->id);

            // }
            $this->slug = $data['slug'];
            $data['page'] = $request->page;
            //$data['events'] = $this->event->needsVolunteers();
            //dd($data['events']);
            $data['volunteer_type'] = $request->volunteer_type;
            $data['event_name'] = $request->event_name;
            $data['event_id'] = $request->event_id;
            return view("cocard-church.church.admin.volunteers",$data);
        }
    }
    public function volunteer_main_list(Request $request,$slug){
        $data['organization']           = $this->organization->getUrl($slug);
        $organization_id = $data['organization']->id;
        $data['slug'] =$slug;
        $data['volunteers'] = $this->volunteer->getvolunteer();
        $data['volunteer_groups'] =$this->volunteer_group->getvolunteer_group();
        $data['volunteer_group_by_field'] = $this->volunteer_group->groupByField('event_id',$organization_id,$request);
        $data['events'] = $this->event->groupByEvent($organization_id,$request);
        // dd($data['event']);
        return view("cocard-church.volunteer.main_list", $data);
    }

    public function volunteer_list(Request $request,$slug){
        $data['organization']           = $this->organization->getUrl($slug);
        $organization_id = $data['organization']->id;
        $data['slug']                   = $this->organization->getUrl($slug)->url;
            #$data['event'] = $this->event->findEvent($request->event_id);
            // $data['events']                 = $this->event->getEventforVolunteer($request, $data['organization']->id);
            $data['events']                 = $this->event->needsVolunteers($organization_id);
            // dd($data['events']);
            $data['volunteers']             = $this->volunteer->getVolunteersAll($request);
            $data['volunteer_group_with_event'] = $this->volunteer_group->findEventWithVolunteerGroup($request);
            #dd($data['volunteers']);
            $data['prev_event']             = '';

            foreach($data['volunteers'] as $volunteer){
                #dd($volunteer);
                $data['volunteer_group']    = $this->volunteer_group->findVolunteerGroup($volunteer->volunteer_group_id);
                $data['event_title']        = $this->event->findEvent($volunteer->volunteer_group_id);
                $data['user_name']          = $this->user->findUser($volunteer->user_id);
                $data['event_date']         = $this->event->findEvent($volunteer->volunteer_group_id);
            }
            $data['search_volunteer_group'] = $request->input('search_volunteer_group');
            $data['volunteer_groups']       =  $this->volunteer_group->findVolunteerGroupPerEvent($request,$request->search_volunteer_group,$organization_id);
            //$data['volunteer_group_by_field'] = $this->volunteer_group->groupByField('type',$organization_id);
            // foreach($data['volunteer_groups'] as $q){
            //     #dd($this->volunteer->findVolunteerPerGroup($q->id));
            // $data['volunteers']             =  $this->volunteer->findVolunteerPerGroup($q->id);

            // }
            $this->slug = $data['slug'];
            $data['page'] = $request->page;
            $data['start_date'] = $request->start_date;
            $data['end_date'] = $request->end_date;
            // $data['events'] = $this->event->needsVolunteers();
            //dd($data['events']);
            return view("cocard-church.volunteer.list",$data);
            //return $data;
    }
    public function eventFilter(Request $request, $slug){
        #dd($request->event_id);
         $data['organization']       = $this->organization->getUrl($slug);
         $organization_id = $data['organization']->id;
        // $auth = $this->activityLog->AuthGate(Gate::allows('view_volunteer'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('view_volunteer'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['slug']               = $this->organization->getUrl($slug)->url;
            $data['prev_event']         = $request->event_id;
            $data['events']             = $this->event->getEvent($request, $data['organization']->id);
            #dd( $this->volunteer_group->findVolunteerGroupPerEvent($request->event_id));
            $data['volunteer_groups']   =  $this->volunteer_group->findVolunteerGroupPerEvent($request->event_id,$organization_id);
            foreach($data['volunteer_groups'] as $q){
                #dd($this->volunteer->findVolunteerPerGroup($q->id));
            $data['volunteers']         =  $this->volunteer->findVolunteerPerGroup($q->id);

            }
            #dd($data['volunteer_groups']);
            // $data['events'] = $this->event->needsVolunteers();
            return view("cocard-church.church.admin.volunteers",$data);
        }
    }
    public function viewVolunteersPerEvent(Request $request, $slug,$eid,$id){
        #dd($request->event_id);
         $data['organization']       = $this->organization->getUrl($slug);
        // $auth = $this->activityLog->AuthGate(Gate::allows('view_volunteer'),Auth::user()->organization_id,$data['organization']->id);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('view_volunteer'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['slug']               = $this->organization->getUrl($slug)->url;
            $data['prev_event']         = $id;
            #dd($data['prev_event']);
            $data['events']             = $this->event->getEvent($request, $data['organization']->id);
            #dd($data['prev_events'] );
            $data['volunteers']         =  $this->volunteer->findVolunteerPerGroup($eid);
            #dd($data['volunteers']);
            // $data['events'] = $this->event->needsVolunteers();
            return view("cocard-church.volunteer.volunteerdetails",$data);
        }
    }
    public function actionApproveVolunteersPerEvent(Request $request, $slug,$id){
         $data['organization']       = $this->organization->getUrl($slug);
        // $auth = $this->activityLog->AuthGate(Gate::allows('view_volunteer'),Auth::user()->organization_id,$data['organization']->id);

        //  if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('view_volunteer'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['slug']           = $this->organization->getUrl($slug)->url;
            $data['prev_event']     = $request->event_id;
            $data['events']         = $this->event->getEvent($request, $data['organization']->id);
            $data['volunteers']     = $this->volunteer->findVolunteerPerGroup($id);
            //dd($this->volunteer->checkNumberApproveVolunteers($id), $this->volunteer->checkVolunteerGroupNeeded($id));
            if($this->volunteer->checkNumberApproveVolunteers($id) < $this->volunteer->checkVolunteerGroupNeeded($id)){

                $this->volunteer->updateVolunteerStatus('Approved',$id,$request);
                $this->activityLog->log_activity(Auth::user()->id,'Approved Volunteer','Approved Volunteer', $data['organization']->id);
                return $this->volunteer->getVolunteersApprovedCountToDisabled($id);
                //return back()->with('message', 'Successfully Approved Volunteer');
            }else{
                return back()->with('error', 'Already Reach the maximum Volunteers for this Volunteer Group!');
            }
        }
    }
    public function actionRejectedVolunteersPerEvent(Request $request, $slug,$id){
         $data['organization']       = $this->organization->getUrl($slug);
        // $auth = $this->activityLog->AuthGate(Gate::allows('view_volunteer'),Auth::user()->organization_id,$data['organization']->id);

        //  if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('view_volunteer'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['slug']           = $this->organization->getUrl($slug)->url;
            $data['prev_event']     = $request->event_id;
            $data['events']         = $this->event->getEvent($request, $data['organization']->id);
            $data['volunteers']     = $this->volunteer->findVolunteerPerGroup($id);

            $this->volunteer->updateVolunteerStatus('Rejected',$id,$request);
            $this->activityLog->log_activity(Auth::user()->id,'Declined Volunteer','Declined Volunteer', $data['organization']->id);

           return back()->with('message', 'Successfully Rejected Volunteer');

        }
    }
    public function add(Request $request){
        $input = $request->except("_token");
        $input['volunteer_group'] = $this->volunteer_group->findVolunteerGroup($request->volunteer_group_id);
        $input['event_id'] = $input['volunteer_group']->event->id;
        $input["event"] = $this->event->findEvent($input['event_id']);
        $data = view("cocard-church.church.templates.volunteer_fields",$input);
        return $data;
    }

    public function delete($id){
        $volunteer = $this->volunteer->findVolunteer($id);
        $event = $volunteer->volunteer_group->event;
        $this->volunteer->cancel($id);
        return redirect('/organization/'.$event->organization->url.'/administrator/events/view-volunteers/'.$event->id)->with("success","Volunteer Successfully Removed.");
    }

    public function uniqueInArray($array,$attribute,$value,$count = false){
        $count = 0;
        foreach($array as $index => $item){
            for ($i=1; $i < count($value); $i++) { 
                if(isset($item[$attribute[$i]]) && $item[$attribute[$i]] == $value[$i]){
                    $count++;
                }
            }
            
        }
        if($count == true){
            return $count;
        }
        return ($count > 0)?false:true;
    }
    public function apply(Request $request){
        //dd($request);
        
        $result = new DataResult();

        $no_slot_group = "";
        $duplicate_email = "";
        $duplicate_input_email = "";
        $no_slot_input_group = [];
        $errors = [];
        $data['event'] = $this->event->findEvent($request->event_id);
        foreach($data['event']->volunteer_groups as $group){
            $no_slot_input_group[$group->id] = 0;
        }


        foreach($request->volunteers as $volunteer){
           // dd($request->volunteers);
            // if($request->include_user && isset($volunteer['user_id']) && $data['event']->checkUnique($volunteer['user_id'],"user_id",$volunteer['volunteer_group_id']) == "false"){
            //         $duplicate_email = Auth::user()->email;
            // }
            if(isset($volunteer['user_id']) && $request->include_user){
                $user = $this->user->findUser($volunteer['user_id']);
                $user_name = $user->first_name.' '.$user->last_name;
                $user_email = $user->email;
                $volunteer['email'] = $user_email;
                $volunteer['name']  = $user_name;
            }
            // dd($user);
            /*
            if($request->include_user && isset($volunteer['user_id']) && $data['event']->checkUnique($volunteer['user_id'],"user_id",$volunteer['volunteer_group_id']) == "false"){
                    $duplicate_email = Api::getUserByMiddleware()->email;
            }

            if($this->volunteer_group->findVolunteerGroup($volunteer['volunteer_group_id'])->available_slots == 0){
                $no_slot_group = $this->volunteer_group->findVolunteerGroup($volunteer['volunteer_group_id']);
            }
            
            if(isset($volunteer['email']) && $data['event']->checkUnique([$volunteer['email'],$volunteer['name']],["email","name"],$volunteer['volunteer_group_id']) == "false"){
                $duplicate_email = $volunteer['name'];
            }

            if(isset($volunteer['email']) && $this->uniqueInArray($request->volunteers,["email","name"],[$volunteer['email'],$volunteer['name']],TRUE) > 1){
                $duplicate_input_email = $volunteer['name'];
            }
            */

            $no_slot_input_group[$volunteer['volunteer_group_id']]++;
        }


        if($duplicate_email != ""){
            $errors[] = ["output" => "false","value" => $duplicate_email,"message" => "You have applied for this Volunteer Group."];

        }

        if($no_slot_group != ""){
            $errors[] = ["output" => "false","value" => $no_slot_group->type,"message" => "Group is already full."];

        }

        if($duplicate_input_email != ""){
            $errors[] = ["output" => "false","value" => $duplicate_input_email,"message" =>"Applying for multiple times is invalid."];

        }
        // foreach($no_slot_input_group as $index => $number){
        //     $volunteer_group = $this->volunteer_group->findVolunteerGroup($index);
        //     if(!isset($request->include_user)){
        //        $number = ($number-1);
        //     }
        //     if($number > $volunteer_group->available_slots){
        //         $errors[] = ["output" => "false","value" => $volunteer_group->type,"message" => "Volunteers".$number .'-----'.$volunteer_group->available_slots." for this group exceeds available slots"];

        //     }
        // };

        if(count($errors) > 0){
            $result->message = $error["message"];
            $result->error = true;

            if($request->type == "json")
            {

                return json_encode($result);
            }
            else
            {
                return json_encode($errors);
            }
        }


        foreach($request->volunteers as $volunteer){

            if(isset($volunteer['user_id'])){
                if($request->include_user){
                    $volunteer["email"] = Api::getUserByMiddleware()->email;
                    $volunteer["name"] = Api::getUserByMiddleware()->first_name.' '. Api::getUserByMiddleware()->last_name;
                    $this->volunteer->apply($volunteer);
                }
            }else{
                if(Api::getUserByMiddleware()){
                    $user_name = Api::getUserByMiddleware()->first_name.' '.Api::getUserByMiddleware()->last_name;
                    if($volunteer["name"] == $user_name && $volunteer["email"] == Api::getUserByMiddleware()->email){
                        $volunteer['user_id'] = Api::getUserByMiddleware()->id;
                        $volunteers = $this->volunteer->findVolunteerPerGroup($volunteer['volunteer_group_id']);
                        
                        foreach ($volunteers as $key => $value) {
                           if($value->user_id != $volunteer['user_id']){
                                $this->volunteer->apply($volunteer);
                           }else{
                               $data['existing_message'] ='Multiple volunteering for '.$volunteer["name"].' was saved only once.';
                           }
                        }
                    }
                    $this->volunteer->apply($volunteer);
                }
                else{
                    $this->volunteer->apply($volunteer);
                }
            }

        }
        $data['slug'] = $request->slug;
        $data['applied'] = true;
        $data['volunteer_group'] = $this->volunteer_group->findVolunteerGroup($request->volunteer_group_id);
        $data["check_applied_user"] = Api::getUserByMiddleware()?$this->event->checkAppliedUser($request->event_id,$request->user_id):0;
        if($request->has('json')){
            return json_encode($data);
        }

        if($request->type == "json")
        {
            $result->message = "Successfully Applied as Volunteer";
            return json_encode($result);
        }
        return view("cocard-church.church.templates.volunteer_group_modal_content",$data);
        //return redirect("/".$slug."/volunteer/");
    }
     public function changeVolunteerStatus(Request $request){
        #dd($request);
        $input = $request->except("_token");
        $input["status"] = ($input["status"] == "Active")?"InActive":"Active";
        $this->volunteer->update($input,$request->id);
        return $input["status"];
    }

    public function changeVolunteerGroupStatus(Request $request){
        return $this->volunteer_group->changeStatus($request->id,$request->status);
    }

    public function volunteerGroupTable(Request $request){
        // dd($request->all());

        if(Api::getUserByMiddleware() == null)
        {
            return $this->AuthenticationError($request);
        }

        $input['slug'] = $request->slug;
        $input['organization'] = $this->organization->getUrl($request->slug);
        $organization_id = $input['organization']->id;
        //dd($events);
        $request->user = 'true';
        $input['start_date'] = $request->start_date;
        $input['end_date']   = $request->end_date;
        $input['volunteer_groups']  = $this->volunteer_group->findVolunteerGroupPerEvent($request,$request->search_volunteer_group,$organization_id);
        //$input['volunteer_groups'] = $this->volunteer_group->getUserVolunteerGroup($request,$organization_id);
        
        $data = Api::displayData($input,"cocard-church.church.templates.volunteer_table",$request);
        return $data;
    }

    public function volunteerGroupList(Request $request){
        $input['slug'] = $request->slug;
        $data['organization'] = $this->organization->getUrl($request->slug);
        $organization_id = $data['organization']->id;
        //dd($events);
        $request->user = 'true';
        // $input['volunteer_groups']  = $this->volunteer_group->findVolunteerGroupPerEvent($request,$request->search_volunteer_group,$organization_id);
        //$input['volunteer_groups'] = $this->volunteer_group->getUserVolunteerGroup($request,$organization_id);
        $input['volunteer_groups']  = $this->volunteer_group->findVolunteerGroupsPerEvent($request,$request->event_id,$organization_id);
        $data = view("cocard-church.church.templates.volunteer_group_list",$input);
        return $data;
    }
    public function volunteerGroupMainTable(Request $request){
        // dd($request->all());
        $input['slug'] = $request->slug;
        $data['organization'] = $this->organization->getUrl($request->slug);
        $organization_id = $data['organization']->id;
        //dd($events);
        //$input['volunteer_groups'] = $this->volunteer_group->getUserVolunteerGroup($request,$organization_id);
        $input['volunteers'] = $this->volunteer->getvolunteer();
        $input['volunteer_groups'] =$this->volunteer_group->getvolunteer_group();
        $request->user = 'true';
        //$input['volunteer_group_by_field'] = $this->volunteer_group->groupByField('type',$organization_id,$request);
        $input['volunteer_group_by_field'] =$this->volunteer_group->groupByField('event_id',$organization_id,$request);
        $input['events'] = $this->event->groupByEvent($organization_id,$request);
       // $input['volunteer_groups']  = $this->volunteer_group->findVolunteerGroupPerEvent($request,$request->search_volunteer_group,$organization_id);
        // dd($input['volunteer_group_by_field'],$organization_id);
        $data = view("cocard-church.church.templates.volunteer_main_table",$input);
        return $data;
    }
    
    public function volunteerDetailTable(Request $request){
        // dd($request->all());
        $input['slug'] = $request->slug;

        $input['volunteers']         =  $this->volunteer->findVolunteerPerGroup($request->volunteer_group_id);
        $data = view("cocard-church.church.templates.volunteer_detail_table",$input);
        return $data;
    }

    public function vg_per_occurrence(Request $request){
        //dd($request->occurrence, $request->event_id);
        $input['volunteer_groups'] = $this->volunteer_group->vg_per_occurrence($request->occurrence,$request->event_id);
        $data = view("cocard-church.church.templates.volunteer_group_table",$input);
        return $data;
    }
    public function sendEmailToMultipleVolunteer(Request $request,$slug){
        $volunteer_group_id = $request->volunteer_group_id;
        $name = $this->volunteer_group->find($volunteer_group_id)->type;
        $email = $request->email;
        $data['organization'] = $this->organization->getUrl($slug);
        $emailStrip =  explode(',', $email);
        $limit = count($emailStrip);
        // for ($i=0; $i < $limit; $i++) {
            Mail::send('cocard-church.email.message',['request' => $request, 'email' => $email,'name' =>$name], function ($m) use ($emailStrip, $request,$email) {
                    $m->to($emailStrip)->subject($request->subject);
            });
        // }
            $this->activityLog->log_activity(Auth::user()->id,'Send Email','Email sent', $data['organization']->id);

        return back()->with('message', 'Message Successfully Sent!');
    }
    public function sendEmailToVolunteerGroup(Request $request,$slug, $id){
               // dd( $request->all(),$slug, $id);
        $data['organization'] = $this->organization->getUrl($slug);
        $volunteer_group = $this->volunteer_group->find($id);
        $volunteer_group_name = $this->volunteer_group->find($id)->type;
        foreach ($volunteer_group->approved_volunteers as $member) {
            Mail::send('cocard-church.email.message',['group_name' => $volunteer_group_name, 'mail' => $member, 'request' => $request], function ($m) use ($volunteer_group, $member, $request) {
                    $m->to($member->email, $member->name)->subject($request->subject);
            });
        }
        $this->activityLog->log_activity(Auth::user()->id,'Sent Email to Email Group','Sent Email to Email Group', $data['organization']->id);

        return back()->with('message', 'Message Successfully Sent!');
    }
    public function sendEmailIndividualVolunteer(Request $request,$slug, $id)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $volunteer = $this->volunteer->find($id);
        $name = $volunteer->name;
            Mail::send('cocard-church.email.message',['email' => $volunteer->email, 'name' => $name, 'request' => $request], function ($m) use ($volunteer, $request) {
                    $m->to($volunteer->email, $volunteer->name)->subject($request->subject);
            });
        $this->activityLog->log_activity(Auth::user()->id,'Sent Email to a member','Sent Email to a member', $data['organization']->id);

        return back()->with('message', 'Message Successfully Sent!');
    }
    public function sendReminderMessageToVolunteerGroup(Request $request, $slug, $id){
        $message = $this->queued_message->sentReminderMessage($request, $id);
        return back()->with('message', 'Message will be send based on your Reminder date!');
    }

    // public function checkDateToSendReminder(){
    //     $now = Carbon::now()->format('d-m-Y H:i');
    //     $messages = $this->queued_message->checkDateToSendReminder();
    //     foreach ($messages as $message) {
    //         $date_time = strtotime($message->reminder_date);
    //         $newformat = date('d-m-Y H:i', $date_time);
    //         if($newformat  == $now){
    //             $email = 'charina@gmail.com';
    //             Mail::send('cocard-church.email.message',['email' => $email, 'request' => $request], function ($m) use ($email) {
    //                 $m->to('csibuco@gmail.com', 'Charina Sibuco')->subject('Reminder!');
    //             });
    //         }
    //     }
    // }
    public function theme($banner,$scheme)
    {
        $data['banner'] = $banner;
        if(!$data['banner']) {
            $data['banner'] ='background.jpg';
        }
        if($scheme == null){
            $data['scheme1'] = '#04191c';
            $data['scheme2'] = '#ffffff';
            $data['scheme3'] = '#222222';
            $data['scheme4'] = '#012732';
            $data['scheme5'] = '#012732';
            $data['scheme6'] = '#222222';
            $data['scheme7'] = '#222222';
            $data['scheme8'] = '#ffffff';
            $data['scheme9'] = '#ffffff';
            $data['scheme10'] = '#ffffff';
        }else{
            $data['scheme1'] = explode(',', $scheme)[0];
            $data['scheme2'] = explode(',', $scheme)[1];
            $data['scheme3'] = explode(',', $scheme)[2];
            $data['scheme4'] = explode(',', $scheme)[3];
            $data['scheme5'] = explode(',', $scheme)[4];
            $data['scheme6'] = explode(',', $scheme)[5];
            $data['scheme7'] = explode(',', $scheme)[6];
            $data['scheme8'] = explode(',', $scheme)[7];
            $data['scheme9'] = explode(',', $scheme)[8];
            $data['scheme10'] = explode(',', $scheme)[9];
        }
        return $data;
    }
}
