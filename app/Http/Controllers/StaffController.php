<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\StaffRepository as Staff;
use Acme\Repositories\OrganizationRepository as Organization;
use Acme\Repositories\UserRepository as Users;
use Acme\Repositories\RoleRepository as Role;
use Acme\Repositories\ActivityLogRepository;
use App\Http\Requests;
use App\UserRole;
use App\User;
use Auth;
use App;
use Gate;
class StaffController extends Controller
{
    protected $slug;
    public function __construct(ActivityLogRepository $activityLog,Staff $staff,Organization $organization,Users $user,Role $role)
    {
        $this->staff = $staff;
        $this->organization = $organization;
        $this->user = $user;
        $this->role = $role;
        $this->auth = Auth::user();
        $this->activityLog = $activityLog;
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }

    public function index($slug, Request $request){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug'] = $this->organization->getUrl($slug)->url;
        
        if(Gate::denies('view_staff'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $organization_id = $this->organization->getUrl($slug)->id;
                $this->slug = $data['slug'];
                $data['items'] = $this->user->allStaffs($organization_id, $request);
                foreach($data['items'] as $item){
                    $item->edit_url = url('/organization/'.$slug.'/administrator/staff/edit/'.$item->id);
                    $item->delete_url = url('/organization/'.$slug.'/administrator/staff/destroy/'.$item->id);
                }
                $data['roles'] = $this->role->getStaffRole($slug);
                $data['display'] = "list";
                $data['staffs'] = "Yes";
                $data['type']   = 'Staff';
                $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
                $data['search'] = $request->input('search');

                return view("cocard-church.church.admin.staffs",$data);
            }else{
                return view('errors.errorpage');
            }

            
        }
    }

    public function create($slug){
        $data['organization'] = $this->organization->getUrl($slug);

        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        if(Gate::denies('add_staff'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $inputs = $this->user->getFillable();
                foreach($inputs as $input){
                    $data[$input] = old($input);
                }

                $data['role_id'] = '2';
                $data['roles'] = $this->role->getStaffRole($slug);
                $data['display'] = "form";
                $data['staffs'] = "Yes";
                $data['slug'] = $this->organization->getUrl($slug)->url;
                $data['organization_id'] = $this->organization->getUrl($slug)->id;
                $data["action"] = url('/organization/'.$slug.'/administrator/staff/store');
                $data['action_name'] = "Add";
                $data['back_url'] = url('organization/'.$slug.'/administrator/staff');
                return view("cocard-church.church.admin.staffs",$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
    }

    public function edit($slug,$id){
        $data['organization'] = $this->organization->getUrl($slug);
        
        if(Gate::denies('edit_staff'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data["display"] = "form";
                $staff = $this->user->findUser($id)->toArray();
                $inputs = $this->user->getFillable();
                $staff["password"] = "";
                // dd($staff["role_id"]);
                foreach($inputs as $input){
                    $data[$input] = old($input)?old($input):$staff[$input];
                }
                $user = App\User::where('id', $id)->first();
                $user_roles = App\UserRole::where('user_id', $id)->where('role_id', $user->Role()->first()->id)->first();
                $data['original_user'] = App\User::where('id', $user_roles->original_user_id)->first();

                $data['staffs'] = "Yes";
                $data["role_id"] = $this->role->getUserRole($id);
                // dd($data["role_id"]);
                $data['roles'] = $this->role->getStaffRole($slug);
                $data['slug'] = $this->organization->getUrl($slug)->url;
                $data["action"] = url('/organization/'.$slug.'/administrator/staff/update/'.$id);
                //$data['organization_id'] = $this->organization->getUrl($slug)->id;
                $data['action_name'] = "Edit";
                $data['id'] = $id;
                $data["birthdate"] = date("m/d/Y", strtotime($data["birthdate"]));
                $data['back_url'] = url('organization/'.$slug.'/administrator/staff');
                return view("cocard-church.church.admin.staffs",$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }

    public function update($slug,Request $request,$id){

        $results = $this->user->update_staff($request, $id);

        if($results['status'] == false)
         {
             return back()->withErrors($results['results'])->withInput();

         }else{

            $this->activityLog->log_activity(Auth::user()->id,'Updated Staff','Updated the Staff details', $request->organization_id);
            return redirect('organization/'.$slug.'/administrator/staff')->with("message","Staff Successfully Updated.");
         }
    }

    public function store(Request $request, $slug){
        $input = $request->except(["slug","_token"]);
        $result = $this->user->staffStore($input, $request);
        if($result['status'] == true){
            $this->activityLog->log_activity(Auth::user()->id,'New Staff','Created new Staff', $request->organization_id);
            return redirect('organization/'.$slug.'/administrator/staff')->with("message","Successfully Added Staff", $result['status']);
        }
        else{
            return back()->withErrors($result['results'])->withInput();
        }
    }

    public function destroy($slug, $id){
        $data['organization'] = $this->organization->getUrl($slug);
        // $auth = $this->activityLog->AuthGate(Gate::allows('delete_staff'),Auth::user()->organization_id,$data['organization']->id);

        if(Gate::denies('delete_staff'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $this->user->deleteUserRole($id);
                $this->activityLog->log_activity(Auth::user()->id,'Deactivated Staff','Deactivated the Staff', $data['organization']->id);
                return redirect(url('/organization/'.$slug.'/administrator/staff/'))->with("message","Staff Successfully Deleted.");
            }else{
                return view('errors.errorpage'); 
            }
            
        }
    }

    public function changeStaffStatus(Request $request){
        $input = $request->except("_token");
        $input["status"] = ($input["status"] == "Active")?"InActive":"Active";
        $this->user->update($input,$request->id);
        return $input["status"];
    }

    public function roleModalDelete(Request $request, $slug, $id){
         // dd($data);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug'] = $this->organization->getUrl($slug)->url;
        $data['item_id'] = $id;
        $data['slug'] = $slug;
        return view("cocard-church.user.templates.role_modal_delete_edit",$data);
    }

}
