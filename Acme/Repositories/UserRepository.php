<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Acme\Helper\AesTrait;
use App\PersonalInfo;
use App\Participant;
use App\Volunteer;
use App\UserRole;
use App\AssignedUserRole;
use App\Role;
use App\Organization;
use Illuminate\Support\Facades\Validator;
use Excel;
use Auth;
use App\ActivityLog;
use App\FamilyMember;
use App\User;
use DB;

class UserRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'Y-m-d';

/**/
    protected $listener;

    use AesTrait;

    public function model(){
        return 'App\User';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function setDate($date){
        return date('Y-m-d', strtotime($date));
    }
    public function findUser($id){
        return $this->model->find($id);
    }

    public function findSuperadmin($request){
        return $this->model->where('email',$request->email)
                            ->where('status','Active')
                            ->where('password',bcrypt($request->password));
    }
    public function allSuperadmin(){
        return $this->model->where("organization_id",0)->where('status','Active')->get();
    }
    public function getUsers($request, $slug)
    {
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('nickname', 'LIKE', '%' . $search . '%')
                    ->orWhere('gender', 'LIKE', '%' . $search . '%')
                    ->orWhere('position', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';
        if($slug != null){
            $organization_id = Organization::where('url', $slug)->first();
            return $query->select('users.*')
            ->where('organization_id', $organization_id->id)
            ->orderBy('users.'.$order_by, $sort)
            ->paginate();
        }
        else{
             return $query->select('users.*')
            ->orderBy('users.'.$order_by, $sort)
            ->paginate();
        }
    }

    public function getUsersMembers($request, $slug, $id)
    {
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('nickname', 'LIKE', '%' . $search . '%')
                    ->orWhere('gender', 'LIKE', '%' . $search . '%')
                    ->orWhere('position', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';
        if($slug != null){
            $organization_id = Organization::where('url', $slug)->first();
            return $query->select('users.*')
            ->where('organization_id', $organization_id->id)
            ->orderBy('users.'.$order_by, $sort)
            ->paginate();
        }
        else{
             return $query->select('users.*')
            ->orderBy('users.'.$order_by, $sort)
            ->paginate();
        }
    }

    public function store(array $input)
    {
        #dd($input);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $messages   = ['required' => 'The :attribute is required',
                        'phone.unique' => 'Phone Number is already taken. Please choose another.',
                        'email.unique' => 'Email Address is already taken. Please choose another.',
                        'same'     => 'Password Mismatch!'];
        $validator  = Validator::make($input, [
                'first_name'        => 'required',
                'last_name'         => 'required',
                'phone'             => 'required|unique:users',
                'email'             => 'required|email|unique:users',
                'password'          => 'required_if:id, 0',
                'confirm'           => 'required|same:password',
            ], $messages);

            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator];
            }
        $input['password'] = bcrypt($input['password']);
        $input['api_token'] = str_random(60);
        $user = $this->model->create($input);
        $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return ['status' => true, 'results' => 'Success'];
    }

    public function staffStore(array $input, $request)
    {
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $messages   = ['required' => 'The :attribute is required',
                        'phone.unique_custom' => 'Phone Number is already taken. Please choose another.',
                        'email.unique_custom' => 'Email Address is already taken. Please choose another.',
                        'same'     => 'Password Mismatch!'];


        $validator  = Validator::make($input, [
                    'first_name'        => 'required',
                    'last_name'         => 'required',
                    'email'             => 'required|unique_custom:users,email,organization_id,'.$request->organization_id.',status,'.'Active',
                    'phone'             => 'required|unique_custom:users,phone,organization_id,'.$request->organization_id.',status,'.'Active',
                    'password'          => 'required',
                    'confirm'           => 'required|same:password',
                    'image'             => 'image|mimes:jpeg,png,jpg,gif,svg'
                ], $messages);

        if($validator->fails()){
                    #return $this->listener->failed($validator, $action, $id);
                    return ['status' => false, 'results' => $validator];
        }
        
        $input['password'] = bcrypt($input['password']);
        $input['api_token'] = str_random(60);

        if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $input['image'] = $imageName;
                $bdate= date("Y-m-d", strtotime($input['birthdate']));
                $input['birthdate'] = $bdate;
                $user = $this->model->create($input);
                $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
                AssignedUserRole::create(['role_id' => $input['role_id'], 'user_id' => $user->id]);
                UserRole::create(['role_id' => $input['role_id'], 'user_id' => $user->id, 'original_user_id' => $assigned_role->id]);

        }else{
            $bdate= date("Y-m-d", strtotime($input['birthdate']));
            $input['birthdate'] = $bdate;
            $user = $this->model->create($input);
            $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
            AssignedUserRole::create(['role_id' => $input['role_id'], 'user_id' => $user->id]);
            UserRole::create(['role_id' => $input['role_id'], 'user_id' => $user->id, 'original_user_id' => $user->id]);
        }



        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return ['status' => true, 'results' => 'Success'];
    }

    public function adminStore(array $input, $request)
    {
        #dd($input);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $messages   = ['required' => 'The :attribute is required',
                        'phone.unique_custom' => 'Phone Number is already taken. Please choose another.',
                        'email.unique_custom' => 'Email Address is already taken. Please choose another.',
                        'same'     => 'Password Mismatch!'];
        $validator  = Validator::make($input, [
                'first_name'        => 'required',
                'last_name'         => 'required',
                'email'                 => 'required|unique_custom:users,email,organization_id,'.$request->organization_id.',status,'.'Active',
                'phone'                 => 'required|unique_custom:users,phone,organization_id,'.$request->organization_id.',status,'.'Active',
                'password'          => 'required',
                'confirm'           => 'required|same:password',
                'image'             => 'image|mimes:jpeg,png,jpg,gif,svg'
            ], $messages);

            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator];
            }
        $input['password'] = bcrypt($input['password']);
        $input['api_token'] = str_random(60);

        if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $input['image'] = $imageName;
                $bdate= date("Y-m-d", strtotime($request->birthdate));
                $input['birthdate'] = $bdate;

                $user = $this->model->create($input);
                $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
                AssignedUserRole::create(['role_id' => 2 , 'user_id' => $user->id]);
                UserRole::create(['role_id' => 2 , 'user_id' => $user->id, 'original_user_id' => $user->id]);

        }else{
            $bdate= date("Y-m-d", strtotime($request->birthdate));
            $input['birthdate'] = $bdate;
            $user = $this->model->create($input);
            $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
            AssignedUserRole::create(['role_id' => 2 , 'user_id' => $user->id]);
            UserRole::create(['role_id' => 2 , 'user_id' => $user->id, 'original_user_id' => $user->id]);
        }



        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return ['status' => true, 'results' => 'Success'];
    }

    public function superadminStore(array $input, $request)
    {
        #dd($input);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $messages   = ['required' => 'The :attribute is required',
                        'phone.unique_custom' => 'Phone Number is already taken. Please choose another.',
                        'email.unique_custom' => 'Email Address is already taken. Please choose another.',
                        'same'     => 'Password Mismatch!'];
        $validator  = Validator::make($input, [
                'first_name'        => 'required',
                'last_name'         => 'required',
                'email'                 => 'required|unique_custom:users,email,organization_id,'.$request->organization_id.',status,'.'Active',
                'phone'                 => 'required|unique_custom:users,phone,organization_id,'.$request->organization_id.',status,'.'Active',
                'password'          => 'required',
                'confirm'           => 'required|same:password',
                'image'             => 'image|mimes:jpeg,png,jpg,gif,svg'
            ], $messages);

            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator];
            }
        $input['password'] = bcrypt($input['password']);
        $input['api_token'] = str_random(60);

        if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);
                $input['image'] = $imageName;

                $user = $this->model->create($input);
                $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
                AssignedUserRole::create(['role_id' => 1, 'user_id' => $user->id]);
                UserRole::create(['role_id' => 1, 'user_id' => $user->id, 'original_user_id' => $user->id]);

        }else{
            $user = $this->model->create($input);
            $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
            AssignedUserRole::create(['role_id' => 1, 'user_id' => $user->id]);
            UserRole::create(['role_id' => 1, 'user_id' => $user->id, 'original_user_id' => $user->id]);
        }

        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Added User";
        $activity->details                              = "Added ".$input['first_name']." ".$input['last_name'];
        $activity->save();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return ['status' => true, 'results' => 'Success'];
    }

    public function back_office_update($request, $id)
    {
            $input      = $request->except(['_token','confirm','slug']);
            $messages   = ['required' => 'The :attribute is required',];

            $validator  = Validator::make($input, [
                'first_name'        => 'required',
                'last_name'         => 'required',
                'phone'             => 'required',
                'email'             => 'required',
            ], $messages);

            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator];
            }
            if($request->password == ''){
                $this->model->where('id',$id)->update([
                                            'first_name' => $input['first_name'],
                                            'last_name' => $input['last_name'],
                                            'middle_name' => $input['middle_name'],
                                            'phone' => $input['phone'],
                                            'email' => $input['email'],
                                            'status' => $input['status'],
                                            'api_token' => str_random(60)
                                        ]);
            }else{
                 $this->model->where('id',$id)->update([
                                            'first_name' => $input['first_name'],
                                            'last_name' => $input['last_name'],
                                            'middle_name' => $input['middle_name'],
                                            'phone' => $input['phone'],
                                            'email' => $input['email'],
                                            'status' => $input['status'],
                                            'password' => bcrypt($input['password']),
                                            'api_token' => str_random(60)
                                        ]);
            }
    }

    public function allStaffs($organization_id, $request){
        // $member = Role::find(3)->users();
        // dd($member->get());
        $query = $this->model->where('users.organization_id', $organization_id)
                            ->join('assigned_user_roles', 'assigned_user_roles.user_id', '=', 'users.id')
                           ->where('assigned_user_roles.role_id', '!=', '3')
                           ->where('assigned_user_roles.status','Active')
                           ->where('users.status','Active')
                           ->groupby('users.id');

        if($request->has('search')){
            $search     = trim($request->input('search'));
            $query->where(function($query) use ($search){
                $query->where('first_name','LIKE','%'.$search.'%')
                    ->orWhere('last_name','LIKE','%'.$search.'%');
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'first_name';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';
        #dd($query);
        return $query->orderBy($order_by, $sort)->paginate();


    }

    public function getFillable(){
        return $this->model->getFillable();
    }

    public function getAdminPerOrg($request,$issd){
        #$query          = Role::find(2)->users();
        $query          = $this->model->join('assigned_user_roles','assigned_user_roles.user_id','=','users.id')
                                    ->where('assigned_user_roles.role_id','=',2)
                                    ->where('assigned_user_roles.status','=','Active')
                                    ->where('users.organization_id','=',$issd)
                                    ->where('users.status', 'Active');
        if($request->has('search')){
            $search     = trim($request->input('search'));
            $query->where(function($query) use ($search){
                $query->where('first_name','LIKE','%'.$search.'%')
                    ->orWhere('last_name','LIKE','%'.$search.'%');
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';
        #dd($query);
        return $query->orderBy($order_by, $sort)->paginate();
    }

    public function getMembersPerOrg($request, $orgId){
        #$query          = Role::find(2)->users();
        $query          = $this->model->where('users.status','Active')
                                        ->where('users.organization_id',$orgId)
                                        ->join('assigned_user_roles', 'assigned_user_roles.user_id', '=', 'users.id')
                                        ->where('assigned_user_roles.role_id', '3')
                                        ->where('assigned_user_roles.status', 'Active');
        if($request->has('search')){
            $search     = trim($request->input('search'));
            $query->where(function($query) use ($search){
                $query->where('first_name','LIKE','%'.$search.'%')
                    ->orWhere('last_name','LIKE','%'.$search.'%')
                    ->orWhere('middle_name','LIKE','%'.$search.'%');
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'first_name';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';
        #dd($query);
        return $query->orderBy($order_by, $sort)->paginate();
    }
    public function getMembers($request){
        #$query          = Role::find(2)->users();
        $query          = Role::find(3)->users()->where('users.status','Active');
        if($request->has('search')){
            $search     = trim($request->input('search'));
            $query->where(function($query) use ($search){
                $query->where('first_name','LIKE','%'.$search.'%')
                    ->orWhere('last_name','LIKE','%'.$search.'%')
                    ->orWhere('nickname','LIKE','%'.$search.'%')
                    ->orWhere('gender','LIKE','%'.$search.'%');
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';
        #dd($query);
        return $query->orderBy($order_by, $sort)->get();
    }
    public function findMember($id){
        return $this->model->find($id);
    }
    public function create(){

        $data['action']                 = route('post_user_register');
        $data['action_name']            = 'Add';
        $data['organization_id']        = old('organization_id');
        $data['first_name']             = old('first_name');
        $data['last_name']              = old('last_name');
        $data['middle_name']            = old('middle_name');
        $data['address']                = old('address');
        $data['city']                   = old('city');
        $data['state']                  = old('state');
        $data['zipcode']                = old('zipcode');
        $data['birthdate']              = old('birthdate');
        $data['gender']                 = old('gender');
        $data['marital_status']         = old('marital_status');
        $data['phone']                  = old('phone');
        $data['email']                  = old('email');
        $data['password']               = old('password');
        $data['image']                  = old('image');

        return $data;
    }

    public function create_user(){

        $data['action_name']            = "Add";
        $data['organization_id']        = old('organization_id');
        $data['first_name']             = old('first_name');
        $data['last_name']              = old('last_name');
        $data['middle_name']            = old('middle_name');
        $data['address']                = old('address');
        $data['city']                   = old('city');
        $data['state']                  = old('state');
        $data['zipcode']                = old('zipcode');
        $data['birthdate']              = old('birthdate');
        $data['gender']                 = old('gender');
        $data['marital_status']         = old('marital_status');
        $data['phone']                  = old('phone');
        $data['email']                  = old('email');
        $data['password']               = old('password');
        $data['image']                  = old('image');
        $data['status']                 = old('status');

        return $data;
    }

  public function create_admin(){

        $data['action']                 = route('post_user_register_admin');
        $data['action_name']            = 'Add';
        $data['organization_id']        = old('organization_id');
        $data['first_name']             = old('first_name');
        $data['last_name']              = old('last_name');
        $data['middle_name']            = old('middle_name');
        $data['address']                = old('address');
        $data['city']                   = old('city');
        $data['state']                  = old('state');
        $data['zipcode']                = old('zipcode');
        $data['birthdate']              = old('birthdate');
        $data['gender']                 = old('gender');
        $data['marital_status']         = old('marital_status');
        $data['phone']                  = old('phone');
        $data['email']                  = old('email');
        $data['password']               = old('password');
        $data['image']                  = old('image');
        $data['status']                 = old('status');

        return $data;
    }

    public function convertToFile($request , $imageName)
    {
        file_put_contents(public_path('images')."/".$imageName.".".$request->imgext, base64_decode($request->imagescripts)); 
    }

    public function save($request, $id){

        $action     = ($id == 0) ? 'post_user_register' : 'post_user_update';


        if($id == 0){

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',
                           'phone.unique_custom' => 'Phone Number is already taken. Please choose another.',
                            'email.unique_custom' => 'Email Address is already taken. Please choose another.',
                           'same'     => 'Password Mismatch!'];

            $validator  = Validator::make($input, [
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required|unique_custom:users,email,organization_id,'.$request->organization_id.',status,'.'Active',
                'phone'                 => 'required|unique_custom:users,phone,organization_id,'.$request->organization_id.',status,'.'Active',
                'password'              => 'required_if:id, 0',
                'password_confirmation' => 'same:password',
                'organization_id'       => 'required',
            ], $messages);

            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator];
            }

            $input['password'] = bcrypt($input['password']);

            if($request->hasFile('image')||$request->has('imagescripts') ){
                if($request->has("type"))
                {
                    $imageName = time().'.'.$request->imgext;
                    $this->convertToFile($request,$imageName);
                }
                else
                {
                    $imageName = time().'.'.$request->image->getClientOriginalExtension();
                    $request->image->move(public_path('images'), $imageName);
                }
                
                $input['image'] = $imageName;
                $bdate= date("Y-m-d", strtotime($request->birthdate));
                $input['birthdate'] = $bdate;

                $user = $this->model->create($input);
                $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
                AssignedUserRole::create(['role_id' => 3 , 'user_id' => $user->id]);
                UserRole::create(['role_id' => 3 , 'user_id' => $user->id, 'original_user_id' => $user->id]);

            }else{
                $bdate= date("Y-m-d", strtotime($request->birthdate));
                $input['birthdate'] = $bdate;
                $user = $this->model->create($input);
                $this->model->where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
                AssignedUserRole::create(['role_id' => 3 , 'user_id' => $user->id]);
                UserRole::create(['role_id' => 3 , 'user_id' => $user->id, 'original_user_id' => $user->id]);
            }

            return ['status' => true, 'results' => 'Success'];

        }else{
            
            $input      = $request->except(['_token','confirm','slug']);
            $messages   = ['required' => 'The :attribute is required',
                           'email.unique_custom_update' => 'Email Address is already taken. Please choose another.',
                           'phone.unique_custom_update' => 'Phone Number is already taken. Please choose another.',];


            $validator  = Validator::make($input, [
                'first_name'        => 'required',
                'last_name'         => 'required',
                'email'             => 'required|unique_custom_update:users,email,organization_id,'.$request->organization_id.',status,'.'Active,id,'.$id,
                'phone'             => 'required|unique_custom_update:users,phone,organization_id,'.$request->organization_id.',status,'.'Active,id,'.$id,
            ], $messages);



            if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
                return ['status' => false, 'results' => $validator];
            }
            
            if($request->password == ''){
                $bdate= date("Y-m-d", strtotime($request->birthdate));

                if($request->hasFile('image')||$request->has('imagescripts') ){
                    if($request->has("type"))
                    {
                        $imageName = time().'.'.$request->imgext;
                        $this->convertToFile($request,$imageName);
                    }
                    else
                    {
                        $imageName = time().'.'.$request->image->getClientOriginalExtension();
                        $request->image->move(public_path('images'), $imageName);
                    }

                    $this->model->where('id',$id)->update([
                        'first_name'    => $input['first_name'],
                        'last_name'     => $input['last_name'],
                        'middle_name'   => $input['middle_name'],
                        'address'       => $input['address'],
                        'city'          => $input['city'],
                        'state'         => $input['state'],
                        'zipcode'       => $input['zipcode'],
                        'gender'        => $input['gender'],
                        'birthdate'     => $bdate,
                        'marital_status'=> $input['marital_status'],
                        'phone'         => $input['phone'],
                        'email'         => $input['email'],
                        'image'         => $imageName,
                        'api_token'     => $input['api_token']
                        ]);
                }else{
                    $this->model->where('id',$id)->update([
                        'first_name'    => $input['first_name'],
                        'last_name'     => $input['last_name'],
                        'middle_name'   => $input['middle_name'],
                        'address'       => $input['address'],
                        'city'          => $input['city'],
                        'state'         => $input['state'],
                        'zipcode'       => $input['zipcode'],
                        'gender'        => $input['gender'],
                        'birthdate'     => $bdate,
                        'marital_status'=> $input['marital_status'],
                        'phone'         => $input['phone'],
                        'email'         => $input['email'],
                        'api_token'     => $input['api_token']
                        ]);
                }

            }else{
                $bdate= date("Y-m-d", strtotime($request->birthdate));
                if($request->hasFile('image')||$request->has('imagescripts') ){
                   
                    if($request->has("type"))
                    {
                        $imageName = time().'.'.$request->imgext;
                        $this->convertToFile($request,$imageName);
                    }
                    else
                    {
                        $imageName = time().'.'.$request->image->getClientOriginalExtension();
                        $request->image->move(public_path('images'), $imageName);
                    }

                    $this->model->where('id',$id)->update([
                        'first_name'    => $input['first_name'],
                        'last_name'     => $input['last_name'],
                        'middle_name'   => $input['middle_name'],
                        'address'       => $input['address'],
                        'city'          => $input['city'],
                        'state'         => $input['state'],
                        'zipcode'       => $input['zipcode'],
                        'gender'        => $input['gender'],
                        'birthdate'     => $bdate,
                        'marital_status'=> $input['marital_status'],
                        'phone'         => $input['phone'],
                        'email'         => $input['email'],
                        'image'         => $imageName,
                        'password'      => bcrypt($input['password']),
                        'api_token'     => $input['api_token']
                        ]);
                }else{
                    $this->model->where('id',$id)->update([
                        'first_name'    => $input['first_name'],
                        'last_name'     => $input['last_name'],
                        'middle_name'   => $input['middle_name'],
                        'address'       => $input['address'],
                        'city'          => $input['city'],
                        'state'         => $input['state'],
                        'zipcode'       => $input['zipcode'],
                        'gender'        => $input['gender'],
                        'birthdate'     => $bdate,
                        'marital_status'=> $input['marital_status'],
                        'phone'         => $input['phone'],
                        'email'         => $input['email'],
                        'password'      => bcrypt($input['password']),
                        'api_token'     => $input['api_token']
                        ]);
                }
            }
            return ['status' => true, 'results' => 'Success'];
        }
        
    }

    public function edit($id){
        $data['action']         = route('post_user_update', $id);
        $data['action_name']    = 'Edit';
        $user                   = $this->model->find($id);

        $data['first_name']     = (is_null(old('first_name'))?$user->first_name:old('first_name'));
        $data['last_name']      = (is_null(old('last_name'))?$user->last_name:old('last_name'));
        $data['middle_name']    = (is_null(old('middle_name'))?$user->middle_name:old('middle_name'));
        $data['gender']         = (is_null(old('gender'))?$user->gender:old('gender'));
        $data['birthdate']      = ((is_null(old('birthdate'))?$user->format_birthdate->format("m/d/Y"):old('birthdate')));
        $data['marital_status'] = (is_null(old('marital_status'))?$user->marital_status:old('marital_status'));
        $data['address']        = (is_null(old('address'))?$user->address:old('address'));
        $data['city']           = (is_null(old('city'))?$user->city:old('city'));
        $data['state']          = (is_null(old('state'))?$user->state:old('state'));
        $data['zipcode']        = (is_null(old('zipcode'))?$user->zipcode:old('zipcode'));
        $data['phone']          = (is_null(old('phone'))?$user->phone:old('phone'));
        $data['email']          = (is_null(old('email'))?$user->email:old('email'));
        $data['status']         = (is_null(old('status'))?$user->status:old('status'));
        $data['image']          = (is_null(old('image'))?$user->image:old('image'));
        $data['password']       = '';


        return $data;
    }
    public function edit_member($id){
        $data['action']         = route('update_member_details', $id);
        $data['action_name']    = 'Edit';
        $data['donation_list']      = $this->model->find($id);

        $data['name']          = (is_null(old('name'))?$data['donation_list']->name:old('name'));
        $data['description']    = (is_null(old('description'))?$data['donation_list']->description:old('description'));
        $data['status']         = (is_null(old('status'))?$data['donation_list']->status:old('status'));

        return $data;
    }
    public function update(array $request, $id){
        $this->model->find($id)->update($request);

    }
    public function update_admin($request,$id){
        $input      = $request->except(['_token','confirm']);
        $messages   = ['required' => 'The :attribute is required',
                        'email.unique_custom_update' => 'Email Address is already taken. Please choose another.',];

        $validator  = Validator::make($input, [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|unique_custom_update:users,email,organization_id,'.$request->organization_id.',status,'.'Active,id,'.$id,
            ], $messages);

        if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        $bdate= date("Y-m-d", strtotime($request->birthdate));
        if($request->password == ''){
            
            #dd($request->hasFile('image'));
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);

                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'image'         => $imageName,
                    'api_token'     => $id.str_random(60)
                    ]);
            }else{
                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'api_token'     => $id.str_random(60)
                    ]);
            }
        }else{
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);

                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'image'         => $imageName,
                    'password'      => bcrypt($input['password']),
                    'api_token'     => $id.str_random(60)
                    ]);
            }else{
                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'password'      => bcrypt($input['password']),
                    'api_token'     => $id.str_random(60)
                    ]);
            }

            // $getuserrole->role_id   = $input['role_id'];
            // $getuserrole->status    = $input['status'];
            // $getuserrole->save();
       }
       if($request->role_id){
            $user_role = new UserRole;
            $getuserrole = $user_role->where('user_id',$id);
            $getuserrole->update([
                'role_id'    => $input['role_id']
                ]);
       }

       return ['status' => true, 'results' => 'Success'];
   }

   public function update_staff($request,$id){
        $input      = $request->except(['_token','confirm']);
        $messages   = ['required' => 'The :attribute is required',
                        'email.unique_custom_update' => 'Email Address is already taken. Please choose another.',
                        'phone.unique_custom_update' => 'Phone Number is already taken. Please choose another.',];

        $validator  = Validator::make($input, [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|unique_custom_update:users,email,organization_id,'.$request->organization_id.',status,'.'Active,id,'.$id,
            'phone'             => 'required|unique_custom_update:users,phone,organization_id,'.$request->organization_id.',status,'.'Active,id,'.$id,
            ], $messages);

        if($validator->fails()){
                #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        $bdate= date("Y-m-d", strtotime($request->birthdate));

        if($request->password == ''){
            
            #dd($request->hasFile('image'));
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);

                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'image'         => $imageName,
                    'api_token'     => $id.str_random(60)
                    ]);
            }else{
                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'marital_status'=> $input['marital_status'],
                    'api_token'     => $id.str_random(60)
                    ]);
            }

        }else{
            if($request->hasFile('image')){
                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $request->image->move(public_path('images'), $imageName);

                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'image'         => $imageName,
                    'password'      => bcrypt($input['password']),
                    'api_token'     => $id.str_random(60)
                    ]);
            }else{
                $this->model->where('id',$id)->update([
                    'first_name'    => $input['first_name'],
                    'last_name'     => $input['last_name'],
                    'middle_name'   => $input['middle_name'],
                    'address'       => $input['address'],
                    'city'          => $input['city'],
                    'state'         => $input['state'],
                    'zipcode'       => $input['zipcode'],
                    'gender'        => $input['gender'],
                    'birthdate'     => $bdate,
                    'marital_status'=> $input['marital_status'],
                    'phone'         => $input['phone'],
                    'email'         => $input['email'],
                    'password'      => bcrypt($input['password']),
                    'api_token'     => $id.str_random(60)
                    ]);
            }


            // $getuserrole->role_id   = $input['role_id'];
            // $getuserrole->status    = $input['status'];
            // $getuserrole->save();
       }

       // if($request->role_id){
       //      $user_role = new UserRole;
       //      $getuserrole = $user_role->where('user_id',$id);
       //      $getuserrole->update([
       //          'role_id'    => $input['role_id']
       //          ]);
       //  }

       //  $user_roles = UserRole::where('user_id', $id)->where('role_id', $input['role_id'])->first();
       //  User::where('id', $user_roles->original_user_id)->first()->update([ 'email' => $input['email'], 'phone' => $input['phone']  ]);

        return ['status' => true, 'results' => 'Success'];
   }

    public function show($id){
        return $this->model->find($id);
    }


    public function destroy($id){
         $this->model->where('id',$id)->update([
                    'status'    => 'InActive'
                    ]);
        //PersonalInfo::where('user_id',$id)->delete();
    }
     public function destroy_user_role($id){
        #dd($id);
        $user_role = new UserRole;
         $user_role->where('user_id',$id)->update([
                    'status'    => 'InActive'
                    ]);
        //PersonalInfo::where('user_id',$id)->delete();
    }

    public function getDonationTransaction($request, $id) {
        //dd($request->input_created_date_timezone[0]);
        Excel::create('Donation Transaction', function($excel) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Event Transaction');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('transactions');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) {

                $donation_transactions = \DB::table('donation')
                ->leftjoin('transaction', 'transaction.id', '=', 'donation.transaction_id')
                ->leftjoin('frequency', 'frequency.id', '=', 'donation.frequency_id')
                ->leftjoin('donation_list', 'donation_list.id', '=', 'donation.donation_list_id')
                ->leftjoin('donation_category', 'donation_category.id', '=', 'donation_list.donation_category_id')
                ->where('transaction.user_id', '=', Auth::user()->id)
                ->select(
                  'donation.donation_type as type',
                  'donation.frequency_id as frequency_id',
                  'donation_category.name as category',
                  'donation_list.name as name',
                  'frequency.title as frequency',
                  'donation.amount as amount',
                  'donation.status as status',
                  'donation.created_at as date')
                ->orderBy('donation.created_at', 'desc')
                ->get();
                $timezone = \Request::get('input_created_date_timezone');
                //dd($timezone);
                $x = 0;
                foreach($donation_transactions as $donation_transaction) {
                    if($donation_transaction->type == 'Recurring' && $donation_transaction->status == 'Active'){
                        $donation_transaction->status = 'On Going';
                    }
                    if($donation_transaction->frequency_id == 0){
                        $donation_transaction->frequency = 'N/A';
                    }
                    if($donation_transaction->category != null){
                        $dateTime = date("n/d/Y g:iA", strtotime($donation_transaction->date));
                         $data[] = array(
                            $donation_transaction->type,
                            $donation_transaction->category,
                            $donation_transaction->name,
                            $donation_transaction->frequency,
                            $donation_transaction->amount,
                            $timezone[$x],
                            $donation_transaction->status,
                              
                        );
                         $x++;
                    }
                    
                }
           
                
                    $sheet->fromArray((isset($data)?$data:''), null, 'A1', false, false);
                    $headings = array('Type', 'Category', 'Name', 'Frequency', 'Amount', 'Date of Transaction', 'Status');
                    $sheet->prependRow(1, $headings);

            });
        })->export('xls');
    }

    public function getEventTransaction($request,$id) {
        //dd($request, $id);
        Excel::create('Event Transaction', function($excel) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Event Transaction');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('transactions');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) {

                $event_participants = \DB::table('users')
                ->join('participants', 'participants.user_id', '=', 'users.id')
                ->join('event', 'event.id', '=', 'participants.event_id')
                ->where('users.id', '=', Auth::user()->id)
                ->select(
                  'event.name as event',
                  'participants.qty as qty',
                  'participants.start_date as participant_start_date',
                  'participants.end_date as participant_end_date',
                  'event.start_date as event_start_date',
                  'event.end_date as event_end_date',
                  'event.fee as fee',
                  'participants.id as id',
                  \DB::raw('(participants.qty * event.fee) as total_amount'),
                  'participants.created_at as date')
                ->orderBy('id', 'desc')
                // ->orderBy('participants.created_at', 'desc')
                ->get();
                $participants = Participant::where('user_id', Auth::user()->id)
                                ->orderBy('participants.created_at', 'desc')
                                ->get();
                //                 // ->toArray();
                // $created_date           = \Request::get('input_created_date_timezone');
                // $start_date             = \Request::get('input_start_date_timezone');
                // $end_date               = \Request::get('input_end_date_timezone');
                $x = 0;
                //dd($start_date,$end_date);
                // foreach($event_participants as $event_participant) {
                //     if($event_participant->fee == 0 || $event_participant->total_amount == 0){
                //         $event_participant->fee = '0';
                //         $event_participant->total_amount = '0';
                //     }
                //         $dateTime = date("n/d/Y g:iA", strtotime($event_participant->date));
                //          $data[] = array(
                //             $input_event_name[$x],
                //             $event_participant->qty,
                //             $input_fee[$x],
                //             $input_total_amount[$x],
                //             $start_date[$x],
                //             $end_date[$x],
                //             $created_date[$x]
                //         );
                //          $x++;
                //     }
                 foreach($participants as $participant) {
                    //dd($participant->event);
                    $participant_start_date     = Carbon::parse($participant->participant_start_date)->format('m/d/Y');
                    $participant_end_date       = Carbon::parse($participant->participant_end_date)->format('m/d/Y');
                    $start_date                 = Carbon::parse($participant->event->start_date)->format('m/d/Y');
                    $end_date                   = Carbon::parse($participant->event->end_date)->format('m/d/Y');
                    $created_date               = Carbon::parse($participant->created_at)->format('m/d/Y');
                    //assign date to no of recurrence
                        $occurence = $participant->event->no_of_repetition;//no of repetition
                        //$start_date = Carbon::parse($participant->event->start_date);//->format('n/j/Y');//start date of recurring event
                        //dd($start_date);
                        //$no_of_occurence_end_date = new Date();
                        switch ($participant->event->recurring) {
                            case 0:
                                $no_of_occurence_end_date = '-';
                                break;
                            case 1://weekly                             
                                //assign new date 
                                $no_of_occurence_end_date = Carbon::parse($start_date)->addWeek($occurence-1);
                                //dd($no_of_occurence_end_date, $occurence, $participant->start_date);
                                break;
                            case 2://monthly
                                //assign new date
                                // $no_of_occurence_end_date = $start_date->addMonth($occurence-1);
                              $no_of_occurence_end_date = Carbon::parse($start_date)->addMonth($occurence-1);
                                break;
                            case 3://yearly
                                //assign new date 
                              $no_of_occurence_end_date = Carbon::parse($start_date)->addYear($occurence-1);
                                
                            break;

                        }
                        //dd($no_of_occurence_end_date,$start_date,$occurence,$participant->event->id);
                        if($participant->event->no_of_repetition == 0){
                            $no_of_occurence_end_date = $participant->event->recurring_end_date;
                        }
                        if($no_of_occurence_end_date != '-'){
                             $no_of_occurence_end_date = Carbon::parse($no_of_occurence_end_date)->format('n/j/Y');
                        }
                       
                        //dd($participant->event->id,$participant->event->no_of_repetition, $no_of_occurence_end_date,$participant->event->recurring_end_date);
                    $total_amount = $participant->event->fee * $participant->qty ;
                    if($participant->event->fee == 0 || $total_amount == 0){
                        $participant->event->fee = '0';
                        $total_amount = '0';
                    }
                    if($participant->event->recurring > 0){
                        $recurring = 'R';
                    }else{
                        $recurring = '-';
                    }

                        if($participant->event->recurring == '0'){
                            $no_of_occurence_end_date = '-';
                        }
                        $total_amount = '$'.number_format($total_amount,2,'.',',');
                        $participant->event->fee = '$'.number_format($participant->event->fee,2,'.',',');
                         $data[] = array(
                            $participant->event->name,
                            $participant->qty,
                            $participant->event->fee,
                            $total_amount,
                            Carbon::parse($participant->start_date)->format('n/j/Y'),
                            Carbon::parse($participant->end_date)->format('n/j/Y'),
                            $recurring,
                            $no_of_occurence_end_date,
                            $created_date
                        );
                         $x++;
                    }

                    $sheet->fromArray((isset($data)?$data:''), null, 'A1', false, false);
                    $headings = array('Event', 'Quantity', 'Fee', 'Total Amount','Event Start Date','Event End Date','Recurring','Recurring End Date', 'Date of Transaction');
                    $sheet->prependRow(1, $headings);

            });
        })->export('xls');
    }

    public function getVolunteerHistory($request, $id) {
       // dd($request);
        Excel::create('Volunteer History', function($excel) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Volunteer History');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('Volunteer History');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) {

                // $volunteer_participants = \DB::table('users')
                // ->join('volunteers', 'volunteers.user_id', '=', 'users.id')
                // ->join('volunteer_groups', 'volunteer_groups.id', '=', 'volunteers.volunteer_group_id')
                // ->join('event', 'event.id', '=', 'volunteer_groups.event_id')
                // ->where('users.id', '=', Auth::user()->id)
                // ->select(
                //   'event.name as event',
                //   'volunteer_groups.type as role',
                //   'event.start_date as start',
                //   'event.end_date as end')
                // ->orderBy('event.start_date', 'asc')
                // ->get();
                $volunteer_participants = Volunteer::where('user_id', Auth::user()->id)
                                ->orderBy('volunteers.created_at', 'desc')
                                ->get();
                $start_date             = \Request::get('input_start_date_timezone');
                $end_date               = \Request::get('input_end_date_timezone');
                $x = 0;
                foreach($volunteer_participants as $volunteer_participant) {

                        $startDateTime = date("n/d/Y g:iA", strtotime($volunteer_participant->start));
                        $endDateTime = date("n/d/Y g:iA", strtotime($volunteer_participant->end));
                         $data[] = array(
                            $volunteer_participant->volunteer_group->event->name,
                            $volunteer_participant->volunteer_group->type,
                            $start_date[$x],
                            $end_date[$x],
                            $volunteer_participant->volunteer_group_status,
                        );
                         $x++;
                    }

                    $sheet->fromArray((isset($data)?$data:''), null, 'A1', false, false);
                    $headings = array('Event', 'Volunteer Role', 'Start Date', 'End Date', 'Status');
                    $sheet->prependRow(1, $headings);

            });
        })->export('xls');
    }
    //assign role to user
    public function assign_role($request, $id){
        $new_role   = Role::where('id',$request)->first();
        $user_name  = $this->model->where('id',$id)->first();


        if(AssignedUserRole::where('user_id',$id)->first()){
            $user_role  = AssignedUserRole::where('user_id',$id)->where('status', 'Active')->get();
            $user_id    = $user_role->first()->user_id;
            $user       = $this->model->where('id', $user_id)->first();
            $role_id    = $user_role->first()->role_id;
            $role       = Role::where('id',$role_id)->first();
            if($role_id != $new_role->id){
                #return $this->assignedRoleToUser($user, $new_role, $request,$id);
                $existing = AssignedUserRole::where('role_id', $new_role->id)
                                            ->where('user_id', $id)
                                            ->where('status', 'Active')
                                            ->first();
                // dd($existing);
                if(is_null($existing)){
                    $exists = AssignedUserRole::where('user_id', $id)->where('role_id', $new_role->id)->where('status', 'InActive')->first();

                    if(count($exists) == 1){
                        AssignedUserRole::where('user_id', $id)->where('role_id', $new_role->id)->update(['status' => 'Active']);
                    }else{
                        $assigned_role  = new AssignedUserRole;
                        $assigned_role->role_id             = $new_role->id;
                        $assigned_role->user_id             = $id;
                        $assigned_role->save(); 
                    }

                    return ['status' => true, 'results' => 'Assigned ' .$user_name->first_name. ' '.$user_name->last_name. ' as '.$new_role->title];

                }
                else{
                    return ['status' => true, 'results' => 'Already assigned role as '.$new_role->title.' for ' . $user->first_name];
                }
            }
            else{
                return ['status' => true, 'results' => 'Already assigned role as '.$new_role->title.' for ' . $user->first_name];
            }
        }else{
            #$this->model->where('id', $id)->first()->assignedRole($request);
            $assigned_role  = new AssignedUserRole;
            $assigned_role->role_id             = $new_role->id;
            $assigned_role->user_id             = $id;
            $assigned_role->save();
           return ['status' => true, 'results' => 'Assigned ' .$user_name->first_name. ' '.$user_name->last_name. ' as '.$new_role->title];
        }
    }

    public function delete($id){
        #$this->model->where('id',$id)->delete();
        #dd($id);
        $user = $this->model->where('id',$id)->first();

        $user->status  = 'InActive';
        $user->save();
    }

    public function deleteMember($id){
        #$this->model->where('id',$id)->delete();
        #dd($id);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $family_members = FamilyMember::where('user_id', $id)->get();

        foreach($family_members as $family_member){
            FamilyMember::where('id', $family_member->id)->first()->update(['user_id' => 0]);
        }

        $this->model->where('id',$id)->first()->update(['status' => 'InActive']);

        
    }

    public function deleteUserRole($id){
         // dd($id);
        $this->model->where('id',$id)->first()->update(['status' => 'InActive']);
        
    }

    public function updateToken($id){
        return $this->model->where('id',$id)->update(['api_token' => $id.'_'.str_random(60)]);
    }
    //Back Office User

    public function back_office_create(){
        //
    }
    public function back_office_edit(){
        //
    }
    public function back_office_delete(){
        //
    }
    public function adminlogin($request,$id){

        $data = $this->model
        ->join('user_roles', 'users.id','user_roles.user_id')
        ->where('users.email','=',$request->emailphone)
        ->where('users.password','=',bcrypt($request->password))
        ->where('users.status','=','Active')
        ->where('user_roles.role_id','=','2')
        ->where('users.organization_id','=',$id)
        ->first();
         dd($data);
    }
    public function userlogin($request){
        #dd('asdsa');
        return \DB::table('users')
        ->join('user_roles', 'users.id','user_roles.user_id')
        ->where('users.password','=',$request->password)
        ->where('users.status','=','Active')
        ->where('user_roles.role_id','=','3')
        ->orWhere('users.email','=',$request->emailphone)
        ->orWhere('users.phone','=',$request->emailphone)
        ->get();
    }
    public function superlogin($request){
        #dd('asdsa');
        return \DB::table('users')
        ->join('user_roles', 'users.id','user_roles.user_id')
        ->where('users.email','=',$request->emailphone)
        ->where('users.password','=',$request->password)
        ->where('users.status','=','Active')
        ->where('user_roles.role_id','=','1')
        ->get();
    }

    public function switchAccount($request){
        UserRole::where('user_id', $request->user_id)->update([ 'role_id' => $request->role_id ]);
    }
}
