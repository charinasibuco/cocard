<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\UserRepository;
use Acme\Repositories\FamilyRepository;
use Acme\Repositories\ActivityLogRepository;
use Acme\Repositories\RoleRepository;
use App\Members;
use Auth;
use App;
use Gate;
use App\User;
class MembersController extends Controller
{
    public function __construct(ActivityLogRepository $activityLog,OrganizationRepository $organization, UserRepository $user, FamilyRepository $family_group, RoleRepository $role){
        $this->middleware('auth');
        $this->organization = $organization;
        $this->user         = $user;
        $this->family_group = $family_group;
        $this->activityLog = $activityLog;
        $this->role = $role;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        

        if(Gate::denies('view_member_list'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data['search'] = $request->input('search');
                $data['type']   = 'Member';
                $data['roles'] = $this->role->getStaffRole($slug);
                $data['family_groups'] = $this->family_group->getFamily($request, $slug);
                $data['members'] = $this->user->getMembersPerOrg($request,$data['organization']->id);
                if($request->type == 'json'){
                    return ($data);
                }
                else{
                  return view('cocard-church.church.admin.members',$data);  
                }
            }else{
                return view('errors.errorpage');
            }
        }
    }

    public function details()
    {
        return view('cocard-church.church.admin.details');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $data = $this->user->create_user();
        $data['action'] = route('post_user_register_admin'); 
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['organization_id'] = $this->organization->getUrl($slug)->id;

        if(Gate::denies('add_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
             $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

             if($auth == true){
                return view('cocard-church.church.admin.member.create',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
     public function viewdetails(Request $request,$slug,$id)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['id']           = $id;  
        $data['member'] = $this->user->findMember($id);

        if(Gate::denies('view_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                return view('cocard-church.church.admin.member.details', $data);
            }else{
                return view('errors.errorpage');
            }
            //$data['events'] = $this->event->getEventDetails($request,$id); 
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit(Request $request,$slug, $id)
    {
        $data = $this->user->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['organization_id']         = $data['organization']->id;
        $data['slug']         = $data['organization']->url;
        

        if(Gate::denies('edit_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.church.admin.member.create',$data);
            }else{
                return view('errors.errorpage');
            }
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function delete(Request $request,$slug,$id)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $user = User::where('id', $id)->first();

        if(Gate::denies('delete_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data = $this->user->deleteMember($id);
                $data['search'] = $request->input('search');
                $data['members'] = $this->user->getMembers($request);
                $this->activityLog->log_activity(Auth::user()->id,'Deactivate Member','Deactivated new member', $user->organization_id);
                return back()->with('message', 'Successfully Deleted Member.');
            }else{
                return view('errors.errorpage');
            }
            //SOFT DELETE
        }
    }
}
