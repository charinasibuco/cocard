<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Acme\Repositories\OrganizationRepository as Organizations;
use Acme\Repositories\UserRepository as Users;
use Acme\Repositories\RoleRepository as Role;
use Acme\Repositories\ActivityLogRepository as ActivityLog;
use Auth;
use App;
use App\Organization;
use App\User;
use Mail;
use Gate;

class AdministratorController extends Controller
{
    public function __construct(Organizations $organization, Users $user,Role $role, ActivityLog $activityLog)
    {
        $this->middleware('auth');
        $this->organization = $organization;
        $this->auth = Auth::user();
        $this->user = $user;
        $this->role = $role;
        $this->activityLog = $activityLog;

        if($this->auth != null){
            App::setLocale($this->auth->locale);
        }
    }
    //List of Administrators
    public function index(Request $request, $id){
        $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
        if(Gate::denies('view_admin_org'))
        {
            return view('errors.errorpage');
        }
        else
        {
            // dd($request->id);

            $data['organizations'] = $this->organization->getActive($request->id);
            #$data['organizations'] = $this->organization->getActive();
            #dd($data["organizations"]);
            #dd($id);
            #dd(count($data['org_admins']));
            $issd = $id;
            foreach($data['organizations'] as $organization){
                $issd = $organization->id;
            }
            $data['admins'] = $this->user->getAdminPerOrg($request, $issd);
            #dd($data['admins']);

            #dd($data["admins"]);
            $data['display'] = 'list';

            #dd($organization->id);
            return view("cocard-church.superadmin.administrators",$data);
        }
    }
    //creating of new admins
    public function create(Request $request, $id){
        if(Gate::denies('add_admin_org'))
        {
            return view('errors.errorpage');
        }
        else{
            $inputs = $this->user->getFillable();
            $data = [];

            foreach($inputs as $input){
                $data[$input] = old($input)?old($input):"";
            }

            // $admin = $this->user->findUser($id)->toArray();
            // $data['image'] = $admin['image'];
            $issd = Organization::where('id', $id)->first();
            $data["organization_id"] = $id;
            $data['url_id']= $issd->pending_organization_user_id;
            $slug = 'none';
            $data['organization'] = $this->organization->getUrl($slug);
            $data["organization_name"] = $this->organization->findOrganization($id)->name;
            $data["role_id"] = $this->role->findRole("administrator")->id;
            #$data["back_url"] = route("organization_admin_users");
            $data["display"] = "form";
            $data["action"] = route("administrator_store");
            $data["action_name"] = "Add";
            $data["user_type"] = "administrator";

            return view("cocard-church.superadmin.administrators", $data);
        }
    }
    //saving of the new admin
    public function store(Request $request)
    {

        $input = $request->except(['_token']);
        $result = $this->user->adminStore($input, $request);

        $organization = Organization::where('id', $request->organization_id)->first();

        if($result['status'] == true){
            $this->activityLog->superadmin_log_activity(Auth::user()->id,'Added Administrator', 'Added '.$request->first_name.' '.$request->last_name.' in '.$organization->name, 0);
            $this->activityLog->log_activity(Auth::user()->id,'Added Administrator', 'Added '.$request->first_name.' '.$request->last_name, $organization->id);
            return redirect('/superadmin/organization-list-of-administrators/'.$request->url_id)->with("success","Successfully Added Administrator", $result['status']);
        }
        else{
            return back()->withErrors($result['results'])->withInput();
        }
    }

    //modify the admin details. display details
    public function edit($id){
        if(Gate::denies('edit_admin_org'))
        {
            return view('errors.errorpage');
        }
        else{
            $data["display"] = "form";
            $admin = $this->user->findUser($id)->toArray();
            $data['image'] = $admin['image'];
            $inputs = $this->user->getFillable();
            $admin["password"] = "";
            $slug = 'none';
            $data['organization'] = $this->organization->getUrl($slug);
            $data["organization_name"] = $this->user->findUser($id)->organization->name;
            foreach($inputs as $input){
                $data[$input] = old($input)?old($input):$admin[$input];
            }
            // dd($id);
            $orga = $this->user->findUser($id);
            $issd = Organization::where('id', $orga->organization_id)->first();
            $data['url_id']= $issd->pending_organization_user_id;
            $data["birthdate"] = date("m/d/Y", strtotime($data["birthdate"]));
            $data["role_id"] = $this->role->findRole("administrator")->id;
            $data["action"] = route('administrator_update',$id);
            $data['action_name'] = "Edit";
            $data['id'] = $id;
            $data['back_url'] = route('organization_admin_users');
            $data["user_type"] = "administrator";
            return view("cocard-church.superadmin.administrators",$data);
        }
    }
    //saving the modified details
    public function update(Request $request,$id)
    {
        $result = $this->user->update_admin($request, $id);
        $organization = Organization::where('id', $request->organization_id)->first();

        if($result['status'] == true){
            $this->activityLog->superadmin_log_activity(Auth::user()->id,'Updated Administrator', 'Updated '.$request->first_name.' '.$request->last_name.' in '.$organization->name, 0);
            $this->activityLog->log_activity(Auth::user()->id,'Updated Administrator', 'Updated '.$request->first_name.' '.$request->last_name, $organization->id);
            return redirect('/superadmin/organization-list-of-administrators/'.$request->url_id)->with("success","Administrator Successfully Updated.");
        }
        else{
            return back()->withErrors($result['results'])->withInput();
        }
        
        #$staff = $this->user->findUser($id);
        #$staff->changeRole($request->role_id);
    }
    //deactivaating the admin
    public function destroy($id){

        $user = User::where('id', $id)->first();
        $organization = Organization::where('id', $user->organization_id)->first();

        if(Gate::denies('add_admin_org'))
        {
            return view('errors.errorpage');
        }
        else{
            $this->user->deleteUserRole($id);
            $this->activityLog->superadmin_log_activity(Auth::user()->id,'Deleted Administrator', 'Deleted '.$user->first_name.' '.$user->last_name.' in '.$organization->name, 0);
            $this->activityLog->log_activity(Auth::user()->id,'Deleted Administrator', 'Deleted '.$user->first_name.' '.$user->last_name, $organization->id);
            return back()->with("success","Administrator Successfully Deleted.");
        }
    }
}
