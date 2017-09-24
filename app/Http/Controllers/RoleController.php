<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\RoleRepository;
use Acme\Repositories\PermissionRepository;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\ActivityLogRepository;
use App\Http\Requests;
use Auth;
use App;
use Gate;

class RoleController extends Controller
{
    public function __construct(ActivityLogRepository $activityLog,RoleRepository $role, OrganizationRepository $organization, PermissionRepository $permissions){
        $this->role = $role;
        $this->organization = $organization;
        $this->permissions  = $permissions;
        $this->activityLog = $activityLog;
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
    public function index(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);

        if(Gate::denies('view_roles'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data['roles'] = $this->role->getStaffRoleList($request, $slug);
                $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
                
                $data['slug']         = $data['organization']->url;
                $data['search'] = $request->input('search');
                #dd($data);
                return view('cocard-church.church.admin.role', $data);
            }else{
                return view('errors.errorpage');
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);

        if(Gate::denies('add_role_permission'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data                 = $this->role->create();
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
                $data['action_name']  = 'Add';
                $data['permissions']    = $this->permissions->getAdminPermission();
                return view('cocard-church.church.admin.roleform', $data);
            }else{
                return view('errors.errorpage');
            } 
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
        $roles = $this->role->save($request);
        $slug       = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);

        if($roles['status'] == true){
            $this->activityLog->log_activity(Auth::user()->id,'New Role','Created new Role', $data['organization']->id);
            return redirect('organization/'.$slug.'/administrator/role')->with('message', 'Successfully Added Role!');
        }
        else{
            return redirect('organization/'.$slug.'/administrator/role/create')->withErrors($roles['results'])->withInput();
        }
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
    public function edit($slug, $id)
    {
        #$role = $this->role->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);

        if(Gate::denies('update_role_permission'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $role                       = $this->role->find($id);
                $data                       = $this->role->edit($id);
                $data['slug']               = $slug;
                $data['organization'] = $this->organization->getUrl($slug);
                $permission_role            = [];
                foreach($role->permission as $permission){
                    $permission_role[]  = $permission->id;
                }
                $data['action_name']  = 'Edit';
                $data['permission_role']    = $permission_role;
                $data['role']               = $role;
                $data['permissions']        = $this->permissions->getPermissionToRole();

                return view('cocard-church.church.admin.roleform', $data);
            }else{
                return view('errors.errorpage');
            }
        }
         // dd($role->permission);
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
        $roles = $this->role->save($request, $id);
        $slug       = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);
        if($roles['status'] == true){
            $this->activityLog->log_activity(Auth::user()->id,'Updated Role','Updated Role Details', $data['organization']->id);
            return redirect('organization/'.$slug.'/administrator/role')->with('message', $roles['results']);
        }
        else{
            return redirect('organization/'.$slug.'/administrator/role')->withErrors($roles['results'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug, $id)
    {
        $data['organization'] = $this->organization->getUrl($slug);

        if(Gate::denies('delete_role_permission'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $this->role->delete($id);
                $this->activityLog->log_activity(Auth::user()->id,'Deactivated Role','Deactivated the Role', $data['organization']->id);
                return redirect('organization/'.$slug.'/administrator/role/')->with('message', 'Succefully Deleted Role');
            }else{
                return view('errors.errorpage');
            } 
        }
    }

    public function deleteUserRole(Request $request){
      // dd($request);
      $this->role->deleteUserRole($request->user_id, $request->role_id);

      return back()->with("message","Role Successfully Deleted.");
    }
}
