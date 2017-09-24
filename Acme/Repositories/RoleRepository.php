<?php
namespace Acme\Repositories;
use App\User;
use App\Role;
use Acme\Repositories\Repository;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\UserRole;
use App\AssignedUserRole;
use App\OrgRole;
use App\Organization;

class RoleRepository extends Repository{
    const LIMIT = 10;

    protected $listener;

    public function model(){
        return 'App\Role';
    }
/**/
    public function setListener($listener){
        $this->listener = $listener;
    }
    public function getRoles(){
        if(Auth::user()->organization_id != 0){
            return $this->model->where("title","staff")->get();
        }
        return $this->model->all();
    }

    public function getStaffRole($slug){

        $org = Organization::where('url', $slug)->first();

        return $this->model->join('org_roles', 'org_roles.role_id', '=', 'roles.id')
                            ->where('org_roles.organization_id', $org->id)
                            ->where('roles.status', '=', 'Active')
                            ->where('org_roles.status', '=', 'Active')
                            ->orWhere('org_roles.organization_id', '0')
                            ->where('roles.id', '!=', '1')
                            ->where('roles.id', '!=', '3')
                            ->get();
    }

    public function getStaffRoleList($request, $slug){

        $org = Organization::where('url', $slug)->first();

        $query = $this->model->join('org_roles', 'org_roles.role_id', '=', 'roles.id')
                            ->where('org_roles.organization_id', $org->id)
                            ->where('roles.status', '=', 'Active')
                            ->where('org_roles.status', '=', 'Active')
                            ->orWhere('org_roles.organization_id', '0')
                            ->where('roles.id', '!=', '1')
                            ->where('roles.id', '!=', '3');

        if($request->has('search')){
            $search     = trim($request->input('search'));
            $query->where(function($query) use ($search){
                $query->where('first_name','LIKE','%'.$search.'%')
                    ->orWhere('last_name','LIKE','%'.$search.'%');
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'title';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';
        #dd($query);
        return $query->orderBy($order_by, $sort)->paginate();
    }

    public function findRole($input){
        $role = $this->model->find($input);
        if(is_null($role)){
            $role = $this->model->where("title",$input)->first();
        }
        return $role;
    }
    public function getRole($request){
        if($request->has('search')){
            return  $query = $this->model->where('name', 'LIKE', '%' . $request->input('search'). '%')
                                ->orWhere('label', 'LIKE', '%' . $request->input('search') . '%')
                                ->select('name','label')
                                ->orderBy('name', $request->input('sort'))
                                ->paginate(self::LIMIT);
        }
        if($request->input('order_by') && $request->input('sort')){
            return Role::orderBy($request->input('order_by'), $request->input('sort'))->paginate(self::LIMIT);
        }
        return Role::paginate(self::LIMIT);
    }

    public function getUserRole($id){
        $user_role = UserRole::where('user_id', $id)->first();
        return $this->model->where('id', '=', $user_role->role_id)->first()->id;
    }

    public function getLoginUserRole(){
        $user_roles = AssignedUserRole::where('user_id', Auth::user()->id)->where('status', 'Active')->get();
        return $user_roles;
        
    }

    public function getMembersRole($id)
    {
        $query =  $this->model->where("title" , "member")->first();

        $user_roles = AssignedUserRole::where('user_id', $id)
                    ->where('status', 'Active')
                    ->where('role_id', $query->id)
                    ->get();

        return $user_roles;
    }

    public function getUserRoleByUserId($id){
        $user_roles = AssignedUserRole::where('user_id', $id)->where('status', 'Active')->get();
        return $user_roles;
    }

    public function create(){

        $data['title']          = old('title');
        $data['description']    = old('description');
        $data['action']         = route('store_role');
        $data['action_button']  = 'Add';
        return $data;
    }

    public function save($request, $id = 0){
        $action         = ($id == 0) ? 'store_role' : 'update_role';
        $input          = $request->all();
        $messages       = ['required'      => 'The :attribute is required'];

        $validator      = Validator::make($input, ['title' => 'required', 'description' => 'required'], $messages);
        if($validator->fails()){
             return ['status' => false, 'results' => $validator];
        }

        $org = Organization::where('url', $request->slug)->first();

        if($id == 0){
            $role       = $this->model->create(['title' => $input['title'], 'description' => $input['description']]);
            if($role)
            {
                if(count($input['permission']) > 0){
                    $role->attachPermission($input['permission']);
                }
            }

            OrgRole::create(['role_id' => $role->id, 'organization_id' => $org->id]);

        }else{
            $role                   = $this->model->with(['Permission'])->find($id);
            $role->title            = $input['title'];
            $role->description      = $input['description'];

            if($role->save())
            {
                $role_permissions            = [];

                foreach($role->permission as $permission){
                    $role_permissions[]  = $permission->id;
                }

                $role->detachPermission($role_permissions);

                if(isset($input['permission'])){
                    $role->attachPermission($input['permission']);
                }
            }
        }
        return ['status' => true, 'results' => 'Success'];
    }

    public function edit($id){
        $data['action']         = route('update_role', $id);
        $data['action_button']  = 'Update';
        $data['role']           = $this->model->with(['Permission'])->find($id);
        $data['title']          = (is_null(old('title'))?$data['role']->title:old('title'));
        $data['description']    = (is_null(old('description'))?$data['role']->description:old('description'));

        return $data;
    }

    public function destroy($id){

        return $this->model->find($id)->delete();
    }
    public function delete($id){
        #$this->model->where('id',$id)->delete();
        //dd($id);
        $data['org_role'] = OrgRole::where('role_id', $id)->update(['status'  => 'InActive']);;
        $data['user_role'] = $this->model->where('id',$id)->update(['status'  => 'InActive']);

        return $data;
    }
    //delete user role from a specific user
    public function deleteUserRole($user_id, $role_id){
        // dd($user_id);
        AssignedUserRole::where('user_id', $user_id)
                        ->where('role_id', $role_id)
                        ->update([ 'status' => 'InActive' ]);

        $exists = UserRole::where('user_id', $user_id)->where('role_id', $role_id)->first();
        if(count($exists) == 1){
            $new_role = AssignedUserRole::where('user_id', $user_id)->where('status', 'Active')->first();
            UserRole::where('user_id', $user_id)->where('role_id', $role_id)->update(['role_id' => $new_role->role_id]);
        }
    }
}
