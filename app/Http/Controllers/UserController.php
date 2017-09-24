<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Acme\Repositories\UserRepository as Users;
use Acme\Repositories\OrganizationRepository as Organizations;
use Acme\Repositories\FamilyRepository as Family;
use Acme\Repositories\EventRepository as Event;
use Acme\Repositories\FrequencyRepository as Frequency;
use Acme\Repositories\DonationRepository as Donation;
use Acme\Repositories\DonationCategoryRepository as DonationCategory;
use Acme\Repositories\DonationListRepository as DonationList;
use Acme\Repositories\ParticipantRepository as Participant;
use Acme\Repositories\TransactionRepository as Transaction;
use Acme\Repositories\RoleRepository as Role;
use Acme\Repositories\VolunteerRepository as Volunteer;
use Acme\Repositories\VolunteerGroupRepository as VolunteerGroup;
use Acme\Repositories\ActivityLogRepository;
use App\Libraries\StringConvert;
use App\Http\Requests;
use App\Organization;
use Auth;
use Gate;
use App;
use Mail;
use Excel;
use Acme\Helper\Api;
use Acme\Common\Constants as Constants;
use Acme\Common\DataResult as DataResult;
use App\FamilyMember;
use App\User;
use Acme\Common\CommonFunction;

class UserController extends Controller
{

    use CommonFunction;

	public function __construct(ActivityLogRepository $activityLog,VolunteerGroup $volunteerGroup,Volunteer $volunteer,Role $role,Users $user, Organizations $organization, Event $event, Donation $donation, Family $family, DonationCategory $donationCategory, Frequency $frequency, DonationList $donationlist, Transaction $transaction, Participant $participant){

		$this->user = $user;
        $this->organization = $organization;
        $this->frequency = $frequency;
        $this->volunteer = $volunteer;
        $this->volunteerGroup = $volunteerGroup;
        $this->donationCategory = $donationCategory;
        $this->donationlist = $donationlist;
		$this->event = $event;
        $this->donation = $donation;
        $this->family = $family;
        $this->participant = $participant;
        $this->transaction = $transaction;
        $this->role = $role;
        $this->activityLog = $activityLog;
        $this->auth = Api::getUserByMiddleware();

        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
	}
    
    public function save(Request $request, $id){
         $slug                 = $request->slug;
         $data['organization'] = $this->organization->getUrl($slug);

        
         if(!$request->has("api_token"))
         {
             $request['api_token'] = $id.str_random(60);
         }

         if($request->has('id'))
         {
            $user_id  = $request['id'];
         }
         else
         {
             $user_id = $id;
         }
         
         $results    = $this->user->save($request,$user_id );

         if(!empty(Auth::guard('api')->user()))
         {
            $result = new DataResult();

            if($results['status'] == false)
            {
                $result->error = true;
                $result->message = $results['results']->errors()->all();
            }
            else
            {
                $result->message = Constants::SUCCESSFULLY_UPDATED_USER;
            }

            return json_encode($result->Iterate());
         }

         if($results['status'] == false)
         {
             #return back()->withErrors($results['results'])->withInput();
             if($request->has('json')){
                $data['status'] = $results['status'];
                $data['error_message'] = $results['results']->errors()->all();

                return response()->json($data);
            }
            else{
                return back()->withErrors($results['results'])->withInput();
            }

         }else{
                if($request->has('json')){
                    return json_encode($results);
                }
                else{
                    if(Auth::user()->hasRole('member')){
                        return redirect('organization/'.$slug.'/user/profile/')->with('message', 'Successfully Edited Profile');
                    }else{
                        $this->activityLog->log_activity(Auth::user()->id,'Update Profile','Updated Members profile', $request->organization_id);
                        return redirect('organization/'.$slug.'/administrator/members')->with('message', 'Successfully Edited Profile');
                    }
                }
        }
    }


    public function index(){
        $data['items'] = $this->user->allSuperadmin();
        foreach($data['items'] as $item){
            $item->edit_url = route('user_edit',$item->id);
            $item->delete_url = route('user_delete',$item->id);
        }
        $data['display'] = "list";
        return view('cocard-church.superadmin.users',$data);
    }


    public function create(){
        $data['image'] = '';
        $inputs = $this->user->getFillable();
        foreach($inputs as $input){
            $data[$input] = old($input);
        }
        $slug = 'none';
        $data['organization'] = $this->organization->getUrl($slug);
        $data["action"] = route('superadmin_store');
        $data['action_name'] = "Add";
        $data['roles'] = $this->role->getRoles();
        $data['display'] = "form";
       // $data['slug'] = $this->organization->getUrl($slug)->url;
        $data['image'] = old('image');
        $data['organization_id'] = 0;
        $data['back_url'] = route('users');
        return view("cocard-church.superadmin.users",$data);
    }

    public function edit($id){
        $data["display"] = "form";
        $slug = 'none';
        $data['organization'] = $this->organization->getUrl($slug);
        $staff = $this->user->findUser($id)->toArray();
        $inputs = $this->user->getFillable();
        $staff["password"] = "";
        foreach($inputs as $input){
            $data[$input] = old($input)?old($input):$staff[$input];
        }
        //$data['slug'] = $this->organization->getUrl($slug)->url;
        $superadmin = $this->user->findUser($id)->toArray();
        $data["image"] = $superadmin['image'];
		$data["birthdate"] = date("m/d/Y", strtotime($data["birthdate"]));
        $data["action"] = route('superadmin_update', $id);
        $data['organization_id'] = 0;
        $data['action_name'] = "Edit";
        $data['id'] = $id;
        if(old("role_id")){
            $data['role_id'] = $this->user->findUser($id)->role()->first()?$this->user->findUser($id)->role()->first()->id:old("role_id");
        }
        $data['roles'] = $this->role->getRoles();
        $data['back_url'] = route('users');
        return view("cocard-church.superadmin.users",$data);
    }

    public function update(Request $request,$id){
        $this->user->back_office_update($request, $id);
        $user = $this->user->findUser($id);
        if(Auth::user()->id == 1){
            return back()->with("success","User Successfully Updated.");
        }
        else{
            $slug                 = $request->slug;
            return $this->back_office_edit($slug, $id);
        }
    }

    public function store(Request $request){
        $input = $request->except(["slug","_token","role"]);
        $results = $this->user->store($input);

        if($results['status'] == false)
         {
             return back()->withErrors($results['results'])->withInput();

         }else{

             return back()->with('message', 'Successfully Added User');
         }
    }

    public function superadminUpdate(Request $request, $id){
        $results = $this->user->update_admin($request, $id);

        if($results['status'] == false)
         {
            return back()->withErrors($results['results'])->withInput();

         }else{
            $this->activityLog->superadmin_log_activity(Auth::user()->id,'Updated User Details', 'Updated details of '.$request->first_name.' '.$request->last_name, 0);
            return redirect('user')->with('message', 'Successfully Edited User');
         }

    }

    public function superadminStore(Request $request){
        $input = $request->except(["slug","_token","role"]);
        $results = $this->user->superadminStore($input, $request);

        if($results['status'] == false)
         {
             return back()->withErrors($results['results'])->withInput();

         }else{
             return redirect('user')->with('message', 'Successfully Added User');
         }
    }

    public function destroy($id){
        $this->user->destroy($id);
        $name = User::where('id', $id)->first();
        if($id ==  Auth::user()->id){
             return redirect('/logout')->with("success","Logged Out");

        }else{
            $this->activityLog->superadmin_log_activity(Auth::user()->id,'Deleted User', 'Deleted '.$name->first_name.' '.$name->last_name , 0);
             return redirect()->route('users')->with('message',"User Successfully Deleted.");
        }

    }

    public function dashboard(Request $request,$slug){
    		// $data['items'] = $this->user->allUsers();
            // foreach($data['items'] as $item){
            //     $item->edit_url = route('user_edit',$item->id);
            //     $item->delete_url = route('user_delete',$item->id);
            // }
            if(Gate::denies('login_user_dashboard') && empty(Auth::guard('api')->user()))
            {
               return view('errors.errorpage');
            }
            else
            {
        		$data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
        		$data['display'] = "list";

				$theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
	            $data = array_merge($data,$theme);

                 return Api::displayData($data,'cocard-church.user.dashboard',$request);
            	#return view('cocard-church.user.dashboard',$data);
            }
    }

    public function multipleUserRole(Request $request,$slug){
        if(Gate::denies('login_user_dashboard'))
        {
            return view('errors.errorpage');
        }else{
            $data['user_roles'] = $this->role->getLoginUserRole();
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;
            $data['display'] = "list";

            $assigned_role = App\UserRole::where('user_id', Auth::user()->id)->first();
            $data['role_id'] = (is_null(old('role_id'))? $assigned_role->role_id : old('role_id'));

            $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
            $data = array_merge($data,$theme);

            return Api::displayData($data,'cocard-church.user.multiple_role',$request);
        }
    }


    public function profile(Request $request, $slug = null){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('view_account') && empty(Auth::guard('api')->user()))
        {
            return view('errors.errorpage');
        }
        else
        {
            if(empty(Auth::guard('api')->user()))
            {
                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
                $user = Auth::user();
            }
            else
            {
                $auth = $this->activityLog->AuthGate(Auth::guard('api')->user()->organization_id,$data['organization']->id);
                $user = Auth::guard('api')->user();
            }

            if($auth == true)
            {
                
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
                $data['profile'] = $this->user->findUser($user->id);
                #return $this->getUserThenReturn($data,'cocard-church.user.profile', $request);
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                $data[Constants::MODULE] = Constants::USER_PROFILE;

                return Api::displayData($data,'cocard-church.user.profile',$request);
            }
            else{
                return view('errors.errorpage');
            }   
        }
    }
    public function editprofile(Request $request,$slug, $id){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('update_account') && empty(Auth::guard('api')->user()))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true)
            {
                $data                 = $this->user->edit($id);
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                return Api::displayData($data,'cocard-church.user.editprofile',$request);
                // return view('cocard-church.user.editprofile',$data);

            }else{
                return view('errors.errorpage');
            }
        }
    }
    public function saveLanguage(Request $request, $id){
       #$user =  User::where('id', $id)->first();
        $user = $this->user->findUser($id);
       $user->locale = $request->locale;
       $user->save();
       return back();
    }
    public function event(Request $request, $slug){
        //dd($request);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('view_event_history') && empty(Auth::guard('api')->user()))
        {
            return $this->AuthenticationError($request);
        }
        else
        {
             if(empty(Auth::guard('api')->user()))
            {
                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            }
            else
            {
                $auth = $this->activityLog->AuthGate(Auth::guard('api')->user()->organization_id,$data['organization']->id);
            }
            
            if($auth == true){
                $data['id']           = $this->auth->id;
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
                $data['participants'] = $this->participant->getParticipant($request, $data['id']);

                //assign date to no of recurrence
                foreach($data['participants'] as $participant){
                    //dd($participant);
                    $occurence = $participant->no_of_repetition;//no of repetition
                    $start_date_ = Carbon::parse($participant->start_date);//start date of recurring event
                    switch ($participant->event_recurring) {
                        case 0:
                            $data['no_of_occurence_end_date'] = $start_date_;
                            break;
                        case 1://weekly                             
                            //assign new date 
                            $data['no_of_occurence_end_date'] = $start_date_->addWeek($occurence-1);
                            //dd($data['no_of_occurence_end_date']);

                            break;
                        case 2:
                            //assign new date 
                            $data['no_of_occurence_end_date'] = $start_date_->addMonth($occurence-1);
                            break;
                        case 3:
                            //assign new date 
                            $data['no_of_occurence_end_date'] = $start_date_->addYear($occurence-1);
                        break;

                    }
                }
                    //dd( $start_date_);

                $data['participants_all'] = $this->participant->getParticipantAll($request, $data['id']);
                $data['event']        = $this->event->getEventParticipant($request);
                $data[Constants::MODULE] = Constants::EVENT_LIST;

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);


                return Api::displayData($data,'cocard-church.user.events',$request);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    public function volunteer(Request $request, $slug){

        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        
        if(Gate::denies('view_volunteer_history') && empty(Auth::guard('api')->user()))
        {
            return $this->AuthenticationError($request);
        }
        else
        {
            if(empty(Auth::guard('api')->user()))
            {
                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            }
            else
            {
                $auth = $this->activityLog->AuthGate(Auth::guard('api')->user()->organization_id,$data['organization']->id);
            }

            if($auth == true){
                $data['id']           = $this->auth->id;
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
                $data['search']       = $request->input('search');
                $data['volunteers']    = $this->volunteer->getUserVolunteer($request);
                $data['volunteers_all']    = $this->volunteer->getUserVolunteerAll($request);
                $data['volunteer_group']    = $this->volunteerGroup->getUserVolunteerGroup($request,$data['organization']->id);
                $data['event']        = $this->event->getEventParticipant($request);
                $data[Constants::MODULE] = Constants::VOLUNTEER_LIST;

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                return Api::displayData($data,'cocard-church.user.volunteer',$request);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    public function cancelVolunteer(Request $request, $slug, $id){
        #dd($id);
        $data['organization']             = $this->organization->getUrl($slug);
        $data['slug']                     = $data['organization']->url;
        $data['volunteers']               = $this->volunteer->updateStatus($id);
        return redirect('/organization/'.$slug.'/user/volunteer')->with('message','Volunteer was successfully Canceled!');
    }
    public function volunteereventdetails($slug){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        return view('cocard-church.volunteer.volunteerdetails',$data);
    }
    
    public function donation(Request $request, $slug){

        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;

        if(Gate::denies('view_donation_history') && empty(Auth::guard('api')->user()))
        {
             return $this->AuthenticationError($request);
        }
        else
        {       
                $id = $this->auth->id;

                $input = $request;
                $input['id'] = $id;

                $data['id']                 = $id;
                $data['organization']       = $this->organization->getUrl($slug);
                $data['slug']               = $data['organization']->url;
                // $data['frequency']          = $this->frequency->getFrequency($request);
                $data['donation_type']      = $request->input('donation_type');
                $data['donations']          = $this->donation->getUserDonation($input);
                $data['donations_all']          = $this->donation->getUserDonationAll($input);
                // $data['donationlist']       = $this->donationlist->getDonationList($request,$data['organization']->id);
                // $data['donationCategory']   = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
                foreach ($data['donations'] as $key => $donation) {
                    $data['newdate'] = $donation->Date;
                }

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                $data["module"] = Constants::DONATION_LIST;
                return Api::displayData($data,'cocard-church.user.donation',$request);
        }
    }
    public function getEventTransaction(Request $request, $id){
        //dd($request);
        $id                     = $this->auth->id;
        $organization           = Organization::where('id', $this->auth->organization_id)->first();
        $slug                   = $organization->url;
        $data['organization']   = $this->organization->getUrl($slug);
        $data['slug']           = $data['organization']->url;
        $participants           = $this->participant->getParticipantAll($request, $id)->toArray();
        $data                   = $this->user->getEventTransaction($request,$id);
        // $data                 = $this->user->getEventTransaction(0, $id);
        return view('cocard-church.user.events',$data);
    }

    public function getVolunteerHistory(Request $request,$id){
        $id = $this->auth->id;
        $organization = Organization::where('id', $this->auth->organization_id)->first();
        $slug = $organization->url;
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data                 = $this->user->getVolunteerHistory($request, $id);
        return view('cocard-church.user.events',$data);
        // dd($slug);
    }

    public function getDonationTransaction(Request $request,$slug, $id){

        #$organization = Organization::where('id', $this->auth->organization_id)->first();
        #$slug = $organization->url;
        #$data['organization'] = $this->organization->getUrl($slug);
        #$data['slug']         = $data['organization']->url;

        $id                       = $this->auth->id;
        $data['organization']     = $this->organization->getUrl($slug);
        $data['organization_id']  = $data['organization']->id;
        $data['slug']             = $data['organization']->url;
        $data                     = $this->user->getDonationTransaction($request,$id);
        return view('cocard-church.user.donation',$data);
        // dd($slug);
    }

    public function assign_role(Request $request, $id){
        $d = $this->user->assign_role($request->role, $id);
        $slug = $request->slug;
        $user = User::where('id', $id)->first();
        #dd($d);
        if($request->type == 'Member'){
            $this->activityLog->log_activity(Auth::user()->id,'Role Assigning','Updated Role to User', $user->organization_id);
          return redirect('/organization/'.$slug.'/administrator/members')->with('message',$d['results']);
        }
        elseif ($request->type == 'Staff') {
            $this->activityLog->log_activity(Auth::user()->id,'Role Assigning','Updated Role to User', $user->organization_id);
           return redirect('/organization/'.$slug.'/administrator/staff')->with('message',$d['results']);
        }

    }

    //Back Office User

    public function back_office_index(Request $request, $slug){
        if(Gate::denies('view_users'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['users'] = $this->user->getUsers($request, $slug);
            $data['slug'] = $slug;
            $data['type']   = 'User';
            $data['organization']   = $this->organization->getUrl($slug);
            $data['slug']           = $slug;
            return view('cocard-church.church.admin.users',$data);
        }
    }
    public function back_office_create($slug){
        if(Gate::denies('add_user'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['slug']           = $slug;
            $inputs = $this->user->getFillable();
            foreach($inputs as $input){
                $data[$input] = old($input);
            }
            $data['roles'] = App\Role::get();
            $data['display'] = "form";
            $data['organization']   = $this->organization->getUrl($slug);
            $data['organization_id'] = $data['organization']->id;
            $data["action"] = route('user_store');
            $data['action_name'] = "Add";
            $data['back_url'] = route('users');
            return view("cocard-church.user.form_back_office",$data);
        }
    }
    public function back_office_edit($slug, $id){
        if(Gate::denies('update_user'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $user                   = $this->user->findUser($id)->toArray();
            $inputs                 = $this->user->getFillable();
            $data['organization']   = $this->organization->getUrl($slug);
            $data['slug']           = $slug;
            $data["display"]        = "form";
            $user["password"]       = "";
            $data["action"]         = route('user_update',$id);
            $data['organization_id']= $data['organization']->id;
            $data['action_name']    = "Edit";
            $data['id']             = $id;
            $data['roles']          = App\Role::get();
            $data['back_url']       = '/organization/'.$slug.'/administrator/users/';
            foreach($inputs as $input){
                $data[$input] = old($input)?old($input):$user[$input];
            }
            if(old("role_id")){
                $data['role_id'] = $this->user->findUser($id)->role()->first()?$this->user->findUser($id)->role()->first()->id:old("role_id");
            }

            return view("cocard-church.user.form_back_office",$data);
        }
    }

    public function back_office_delete($slug, $id){
        if(Gate::denies('delete_user'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $this->user->delete($id);
            return back();
        }
    }
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
