<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\EmailGroupRepository;
use Acme\Repositories\EmailGroupMemberRepository;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\ActivityLogRepository;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\User;
use App\EmailGroupMember;
use Auth;
use Gate;
use App;
use Mail;
use Carbon\Carbon;

class EmailController extends Controller
{
    public function __construct(ActivityLogRepository $activityLog,EmailGroupRepository $email_group, EmailGroupMemberRepository $email_group_member, OrganizationRepository $organization){
        $this->middleware('auth');
        $this->email_group = $email_group;
        $this->email_group_member = $email_group_member;
        $this->activityLog = $activityLog;
        $this->organization = $organization;
        $this->auth = Auth::user();

        if($this->auth != null){
            App::setLocale($this->auth->locale);
        }
    }
    //Get the list of all the email groups within the Organization
    public function index_email_group(Request $request, $slug){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['group_member_emails']='';
        if(Gate::denies('view_email_group'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data['slug']  = $slug;
                $data['email_groups']  = $this->email_group->getEmailGroup($request, $slug);

                foreach ($data['email_groups']  as $value) {
                    #foreach($value->EmailGroupMember as $q){
                        $getEmailMem = $this->email_group_member->getEmailGroupMemberA($request,$value->id);
                      foreach ($value->EmailGroupMember as $v) {
                         $data['group_member_emails'] = $this->email_group_member->getEmailGroupMember($request, $value->id);
                      }
                    #}
                } 
                // dd($data['family_groups']);
                return view('cocard-church.email.index',$data);
            }else{
                return view('errors.errorpage');
            } 
        }
    }
    //Get all the list of the members within the Email Group for each Organization
    public function index_email_group_member(Request $request, $slug, $id)
    {
        $data = $this->email_group->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['email_group_id'] = $id;
        $data['slug']  = $slug;
        $data['now']   = Carbon::now()->format('Y-m-d');
        $data['age']   = ($request->search_by_age != null)? $request->search_by_age :'All Age';
        $data['from']  = $request->from;
        $data['to']    = $request->to;
        $data['gender']= $request->search_by_gender;
        $data['marital_status']=$request->search_by_marital_status;
        $data['email_group_members'] = $this->email_group_member->getEmailGroupMember($request, $id);
        $data['email_group'] = $this->email_group->find($id);

        if(Gate::denies('view_email_member'))
        {
            return view('errors.errorpage');
        }
        else
        {          
            // dd($data);
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.email.members.index',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    //Creating new Email group under the Organization
    public function create_email_group(Request $request, $slug){
        $data                 = $this->email_group->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['cb_val']       = isset($request->cb_val)? $request->cb_val : old('cb_val');
        // dd($data['cb_val']);
        // dd($request->cb_val);

        if(Gate::denies('add_email_group'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.email.form',$data);
            }else{
                return view('errors.errorpage');
            } 
        }
    }
    //Add new member the the Email Group. showing of the blade file
    public function create_email_group_member(Request $request,$slug, $id)
    {
        $data                 = $this->email_group_member->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['email_group_id']    = $id;

        if(Gate::denies('add_email_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.email.members.form',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
     //Add new member the the Email Group. showing of the blade file// filtered by not members of the email group
    public function create_email_group_member_filter(Request $request,$slug, $id)
    {
        $data                 = $this->email_group_member->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['email_group_id']    = $id;
        $data['now']   = Carbon::now()->format('Y-m-d');
        $data['age']   = ($request->search_by_age != null)? $request->search_by_age :'All Age';
        $data['from']  = $request->from;
        $data['to']    = $request->to;
        $data['gender']= $request->search_by_gender;
        $data['marital_status']=$request->search_by_marital_status;
        //dd($id);
        $data['email_group_members'] = $this->email_group_member->getEmailGroupMemberFilter($request, $id);
        $data['email_group'] = $this->email_group->find($id);
        if(Gate::denies('add_email_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.email.members.form_filter',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
     //Add new member the the Email Group. showing of the blade file// filtered by not members of the email group//per line
    public function save_email_group_member_filter_single(Request $request,$slug, $id=0,$user_id=0)
    {
        //dd($request,$slug,$id,$user_id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        // if(isset($request['row_selected'][$report_id]))
        // {
        // // do stuff with here with checked items
        // }
        //dd($request['row_selected']);
        if($request['row_selected'] == null){
            return redirect('/organization/'.$slug.'/administrator/email-group/'.$id.'/members/create/filter')->with('message', 'Please select atleast 1 member to add.');
        }
         foreach ($request['row_selected'] as $key => $row) {
           $data = $this->email_group_member->save_individual($id,$row);
         }
        
            #return redirect('/organization/'.$slug.'/administrator/email-group/'.$id.'/members/create/filter')->with('message', 'Successfully Added to this Email Group!');
            return redirect('/organization/'.$slug.'/administrator/email-group/'.$id)->with('message', 'Successfully Added to this Email Group!');

    }
    //Saving of the Email group to db.
    public function store_email_group(Request $request,$id=0){
        
        $slug                   = $request->slug;
        $results                = $this->email_group->save($request,$id);
        $data['organization'] = $this->organization->getUrl($slug);

        if($results['status'] == false)
        {
            return back()->withErrors($results['results'])->withInput();
        }else{
            if($id > 0){
                $this->activityLog->log_activity(Auth::user()->id,'Updated Email Group','Updated Email Group fields', $data['organization']->id);
                return redirect('/organization/'.$slug.'/administrator/email-group')->with('success', 'Changes Saved!');
            }else{
                $this->activityLog->log_activity(Auth::user()->id,'Added Email Group','Added Email Group fields', $data['organization']->id);

            }
            if($request->cb_val == 1){
                return redirect('/organization/'.$slug.'/administrator/email-group/create')->with('messagess', 'Successfully Added Email Group!');
            }
            return redirect('/organization/'.$slug.'/administrator/email-group')->with('message', 'Successfully Added Email Group!');

            #$this->activityLog->log_activity(Auth::user()->id,'Added Email Group','Added Email Group');
            #return back()->with('message', 'Successfully Added Email Group');
        }
        
    }
    //Saving of the added member to the email group
    public function store_email_group_member(Request $request,$id=0, $mid=0)
    {
        #dd($id .'-'.$mid);
        $slug                 = $request->slug;
        $id                   = $request->email_group_id;
        $data['organization'] = $this->organization->getUrl($slug);

        #dd($request->cb_val);
        $results              = $this->email_group_member->save($request);

        if($results['status'] == false)
        {
            return back()->withErrors($results['results'])->with('message','Member is already in this Email Group');
        }else{
            if($request->cb_val == 1){
                #dd('asdasd');
                $this->activityLog->log_activity(Auth::user()->id,'Added Member to Email Group','Added Member to Email Group',$data['organization']->id);

                return redirect('/organization/'.$slug.'/administrator/email-group/'.$id.'/members/create')->with('message', 'Email successfully added!');
            }  #dd('asdafffffsd');
            $this->activityLog->log_activity(Auth::user()->id,'Added Member to Email Group','Added Member to Email Group', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/email-group/'.$id)->with('message', 'Email successfully added!');
            #return back()->with('message', 'Successfully Added Member');
        }
    }
    //display the details of the email group for modification
    public function edit_email_group($slug, $id){
        $data                 = $this->email_group->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('edit_email_group'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.email.form',$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
    //display the details of the member under the email group for modification
    public function edit_email_group_member($slug,$id, $mid)
    {
        $data                 = $this->email_group_member->edit($mid);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['group_id']     = $id;

        if(Gate::denies('edit_email_member'))
        {
            return view('errors.errorpage');
        }
        else
        { 
            // dd($id);
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.email.members.form',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    //saving of the update in email group details
    public function update_email_group(Request $request, $id){

        $slug                 = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);
        $results              = $this->email_group->save($request, $id);

        if($results['status'] == false)
        {
            return back()->withErrors($results['results'])->withInput();
        }else{
                $this->activityLog->log_activity(Auth::user()->id,'Updated Email Group','Updated the Email Group', $data['organization']->id);

            return redirect('/organization/'.$slug.'/administrator/email-group')->with('message', 'Successfully Updated Email Group');
            
        }
        
    }
    //saving the the update in email group member
    public function update_email_group_member(Request $request, $id)
    {
        $slug                 = $request->slug;
        $id                   = $id;
        $data['organization'] = $this->organization->getUrl($slug);
        $results              = $this->email_group_member->save($request, $id);
        $group_id             = $request->group_id;
        if($results['status'] == false)
        {
            return back()->withErrors($results['results'])->withInput();
        }else{
                $this->activityLog->log_activity(Auth::user()->id,' Updated Member','Updated Member in an Email Group', $data['organization']->id);

            //return back()->with('message', 'Successfully Edited Member');
            return redirect('/organization/'.$slug.'/administrator/email-group/'.$group_id)->with('message', 'Successfully Done!');

        }
    }
    //deactivating the email group
    public function delete_email_group($slug, $id){
        $data['organization'] = $this->organization->getUrl($slug);

        if(Gate::denies('delete_email_group'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $this->email_group->softDelete($id);
                $this->activityLog->log_activity(Auth::user()->id,'Deactivated Email Group','Deactivated the Email Group', $data['organization']->id);
                return back()->with('message', 'Successfully Deleted');
            }else{
                return view('errors.errorpage');
            }
            
        }
        
    }
    //remove a member in an email group
    public function delete_email_group_member($slug, $id=0, $mid=0)
    {
        #dd($id.'-'.$mid);
        $data['organization'] = $this->organization->getUrl($slug);

        if(Gate::denies('delete_email_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $this->email_group_member->softDelete($mid);
                $this->activityLog->log_activity(Auth::user()->id,'Deleted member','Deleted Member in an Email Group', $data['organization']->id);

                return back()->with('message', 'Successfully Deleted');
            }else{
                return view('errors.errorpage');
            }
        }
    }
    //sending of email to a specific group
    public function sendEmailGroup(Request $request,$slug, $id)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $email_groups = $this->email_group->find($id);
        foreach ($email_groups->EmailGroupMember as $member) {
            Mail::send('cocard-church.email.message',['group_name'=>$email_groups->name,'email_groups' => $email_groups, 'member' => $member, 'request' => $request], function ($m) use ($email_groups, $member, $request, $data) {
                    $m->to($member->email, $member->name)->subject($request->subject);
            });
            
        } 
        $this->activityLog->log_activity(Auth::user()->id,'Sent Email to group','Sent Email to '. $email_groups->name .' group', $data['organization']->id);
        return back()->with('message', 'Message Successfully Sent!');
    }
    //sending of email individually
    public function sendEmailIndividual(Request $request,$slug, $id)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $email_groups_member = $this->email_group_member->find($id);
        $name = $email_groups_member->name;
            Mail::send('cocard-church.email.message',['email_groups_member' => $email_groups_member,'name'=>$name, 'request' => $request], function ($m) use ($email_groups_member, $request) {
                    $m->to($email_groups_member->email, $email_groups_member->name)->subject($request->subject);
            });
        $this->activityLog->log_activity(Auth::user()->id,'Sent Email to a member','Sent Email to a member', $data['organization']->id);

        return back()->with('message', 'Message Successfully Sent!');
    }
    //sending of email to several persons
    public function sendEmailMultiple(Request $request,$slug, $id)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $email_groups = $this->email_group->find($id);
        $email = $request->email;
        //explode array or emails
        $emailStrip =  explode(',', $email);
        $limit = count($emailStrip);
        if($limit > 1){
            $group_name = $email_groups->name;
        }elseif ($limit == 1) {
            $group_name = '';
        }
        #dd($array);
        for ($i=0; $i < $limit; $i++) { 
            //sending of email
            $name = EmailGroupMember::where('email',$emailStrip[$i])->first()->name;
            Mail::send('cocard-church.email.message',['array' => $emailStrip,'group_name'=>$group_name, 'request' => $request], function ($m) use ($emailStrip, $request, $name, $i) {
                    $m->to($emailStrip[$i], $name)->subject($request->subject);
            
            });
            $this->activityLog->log_activity(Auth::user()->id,'Sent Email to a member','Sent Email to ' .$name.' member ', $data['organization']->id);
        }
        return back()->with('message', 'Message Successfully Sent!');
    }
}
