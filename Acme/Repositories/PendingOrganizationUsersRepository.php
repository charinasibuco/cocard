<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\PendingOrganizationUser;
use App\Organization;
use App\ActivityLog;
use App\User;
use App\UserRole;
use App\AssignedUserRole;
use Auth;
use DB;
use Mail;

class PendingOrganizationUsersRepository extends Repository{

    const LIMIT                 = 20; 
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';
/**/
    protected $listener;

    public function model(){
        return 'App\PendingOrganizationUser';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }
    public function getPendingOrganizationUserDeclined($request)
    {
      $query = $this->model->where('status', 'Declined');
      if ($request->has('search')) {
        $search = trim($request->input('search'));
        $query = $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
            ->orWhere('position', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('url', 'LIKE', '%' . $search . '%')
            ->orWhere('status', 'LIKE', '%' . $search . '%')
            ->paginate();
        });
    }

    $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
    $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

    return $query->select('pending_organization_user.*')
    ->orderBy('pending_organization_user.'.$order_by, $sort)
    ->paginate();
}
        public function getPendingOrganizationUserPending($request)
    {
      $query = $this->model->where('status', 'Pending');
      if ($request->has('search')) {
        $search = trim($request->input('search'));
        $query = $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
            ->orWhere('position', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('url', 'LIKE', '%' . $search . '%')
            ->orWhere('status', 'LIKE', '%' . $search . '%')
            ->paginate();
        });
    }

    $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
    $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

    return $query->select('pending_organization_user.*')
    ->orderBy('pending_organization_user.'.$order_by, $sort)
    ->paginate();
}
    public function getPendingOrganizationUserInactive($request)
    {
      $query = $this->model->where('status', 'InActive');
      if ($request->has('search')) {
        $search = trim($request->input('search'));
        $query = $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
            ->orWhere('position', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('url', 'LIKE', '%' . $search . '%')
            ->orWhere('status', 'LIKE', '%' . $search . '%')
            ->paginate();
        });
    }

    $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
    $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

    return $query->select('pending_organization_user.*')
    ->orderBy('pending_organization_user.'.$order_by, $sort)
    ->paginate();
}
 public function getPendingOrganizationStatus($request,$status)
    {
        if($status == "All"){
            $query = $this->model;
        }else{
            $query = $this->model->where('status', $status);
        }
      if ($request->has('search')) {
        $search = trim($request->input('search'));
        $query = $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
            ->orWhere('position', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('url', 'LIKE', '%' . $search . '%')
            ->orWhere('status', 'LIKE', '%' . $search . '%')
            ->paginate();
        });
    }

    $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
    $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

    return $query->select('pending_organization_user.*')
    ->orderBy('pending_organization_user.'.$order_by, $sort)
    ->paginate();
}
 public function getPendingOrganizationUserActive($request)
    {
      $query = $this->model->where('status', 'Active');
      if ($request->has('search')) {
        $search = trim($request->input('search'));
        $query = $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
            ->orWhere('position', 'LIKE', '%' . $search . '%')
            ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('url', 'LIKE', '%' . $search . '%')
            ->orWhere('status', 'LIKE', '%' . $search . '%')
            ->paginate();
        });
    }

    $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
    $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

    return $query->select('pending_organization_user.*')
    ->orderBy('pending_organization_user.'.$order_by, $sort)
    ->paginate();
}
public function getAllOrganizationUser($request)
{
  if($request != null){
            if($request->has('search')){
                return  $this->model->where('contact_person', 'LIKE', '%' . $request->input('search') . '%')
        ->orWhere('name', 'LIKE', '%' . $request->input('search') . '%')
        ->orWhere('position', 'LIKE', '%' . $request->input('search') . '%')
        ->orWhere('contact_number', 'LIKE', '%' . $request->input('search') . '%')
        ->orWhere('email', 'LIKE', '%' . $request->input('search') . '%')
        ->orWhere('url', 'LIKE', '%' . $request->input('search') . '%')
        ->orWhere('status', 'LIKE', '%' . $request->input('search') . '%')
        ->paginate(10);
            }
        
        if($request->input('order_by') && $request->input('sort')){
            return $this->model->orderBy($request->input('order_by'), $request->input('sort'))->paginate(10);
            }
        }
        return $this->model->paginate(10);
    }

public function create(){

    $data['action']                = route('pending_organization_store');
    $data['action_name']           = 'Add';

    $data['name']                  = old('name');
    $data['contact_person']        = old('contact_person');
    $data['position']              = old('position');
    $data['url']                   = old('url');
    $data['contact_number']         = old('contact_number');
    $data['email']                 = old('email');
    $data['password']              = old('password');
    $data['status']                = old('status');

    return $data;
}

public function save($request, $id = 0){
    $action     = ($id == 0) ? 'pending_organization_store' : 'pending_organization_update';

    $input      = $request->except(['_token','confirm']);

    $input['url'] = lcfirst(preg_replace('/\s+/', '-',(strtolower($input['url']))));

    $messages   = [
    'required' => 'The :attribute is required',
    'same'     => 'Password Mismatch!',
    'unique'   => 'The :attribute is already taken. Please use another.'

    ];
    $validator  = Validator::make($input, [
        'name'                      => 'required',
        'contact_person'            => 'required',
        'position'                  => 'required',
        'url'                       => 'required|unique:pending_organization_user,url',
        'contact_number'            => 'required',
        'password'                  => 'required',
        'password_confirmation'     => 'required|same:password',
        'email'                     => 'required|unique:pending_organization_user,email',
        ], $messages);

    if($validator->fails()){
            #return $this->listener->failed($validator, $action, $id);
        return ['status' => false, 'results' => $validator];
    }

    if($id == 0){
            //dd($input);
        $this->model->create([
                            'name'              => $input['name'], 
                            'contact_person'    => $input['contact_person'],
                            'position'          => $input['position'], 
                            'contact_number'    => $input['contact_number'],
                            'email'             => $input['email'], 
                            'url'               => $input['url'],
                            'status'            => 'Pending',
                            'password'          => bcrypt($input['password'])
                        ]);
            #$this->listener->setMessage('User is successfully created!');
    }else{
        $this->model->where('id',$id)->update($input);
           #$this->listener->setMessage('User is successfully updated!');
    }

        #return $this->listener->passed($action, $id);
    return ['status' => true, 'results' => 'Success'];
}
public function saveReview($request, $id = 0){
    #$action     = ($id == 0) ? 'pending_organization_store' : 'pending_organization_update';
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    $input      = $request->except(['_token','confirm']);

    $input['url'] = lcfirst(preg_replace('/\s+/', '-',(strtolower($input['url']))));

    $messages   = [
    'required' => 'The :attribute is required',
    'unique_custom_update_organization' => 'This :attribute is already taken. Please use another.',
    ];
    $validator  = Validator::make($input, [
        'name'     => 'required',
        'contact_person'    => 'required',
        'position'         => 'required',
        'url'              => 'required|unique_custom_update_organization:pending_organization_user,url,id,'.$id,
        'contact_number'    => 'required|unique_custom_update_organization:pending_organization_user,contact_number,id,'.$id,
        'email'            => 'required|unique_custom_update_organization:pending_organization_user,email,id,'.$id,
        ], $messages);

    if($validator->fails()){
            #return $this->listener->failed($validator, $action, $id);
        return ['status' => false, 'results' => $validator];
    }

    if($id == 0){
            //dd($input);
        $this->model->create($input);
        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Added Organization";
        $activity->details                              = "Added ".$input['name'];
        $activity->org_id                               = 0;
        $activity->save();
            #$this->listener->setMessage('User is successfully created!');
    }else{
        $this->model->where('id',$id)->update($input);
        $org        = new Organization;
        $input      = $request->except(['_token','confirm','email']);
        $input['url'] = lcfirst(preg_replace('/\s+/', '-',(strtolower($input['url']))));

        $org->where('pending_organization_user_id',$id)->update($input);
         $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Updated Organization Details";
        $activity->details                              = "Updated ".$input['name'];
        $activity->org_id                               = 0;
        $activity->save();
           #$this->listener->setMessage('User is successfully updated!');
    }

        #return $this->listener->passed($action, $id);
    return ['status' => true, 'results' => 'Success'];
}
public function edit($id){
    $data['action']                         = route('pending_organization_update', $id);
    $data['action_name']                    = 'Edit';
    $data['pending_organization_user']      = $this->model->find($id);

    $data['name']                           = (is_null(old('name'))?$data['pending_organization_user']->name:old('name'));
    $data['contact_person']                 = (is_null(old('contact_person'))?$data['pending_organization_user']->contact_person:old('contact_person'));
    $data['position']                       = (is_null(old('position'))?$data['pending_organization_user']->position:old('position'));
    $data['url']                            = (is_null(old('url'))?$data['pending_organization_user']->url:old('url'));
    $data['contact_number']                 = (is_null(old('contact_number'))?$data['pending_organization_user']->contact_number:old('contact_number'));
    $data['email']                          = (is_null(old('email'))?$data['pending_organization_user']->email:old('email'));
    $data['password']                       = (is_null(old('password'))?$data['pending_organization_user']->password:old('password'));
    $data['status']                         = (is_null(old('status'))?$data['pending_organization_user']->status:old('status'));

    return $data;
}
public function reviewPending($id){
   $data['action']                          = '';
    $data['action_name']                    = 'Edit';
    $data['pending_organization_user']      = $this->model->find($id);

    $data['name']                           = (is_null(old('name'))?$data['pending_organization_user']->name:old('name'));
    $data['id']                             = $data['pending_organization_user']->id;
    $data['contact_person']                 = (is_null(old('contact_person'))?$data['pending_organization_user']->contact_person:old('contact_person'));
    $data['position']                       = (is_null(old('position'))?$data['pending_organization_user']->position:old('position'));
    $data['url']                            = (is_null(old('url'))?$data['pending_organization_user']->url:old('url'));
    $data['contact_number']                 = (is_null(old('contact_number'))?$data['pending_organization_user']->contact_number:old('contact_number'));
    $data['email']                          = (is_null(old('email'))?$data['pending_organization_user']->email:old('email'));
    $data['password']                       = (is_null(old('password'))?$data['pending_organization_user']->password:old('password'));
    $data['status']                         = (is_null(old('status'))?$data['pending_organization_user']->status:old('status'));

    return $data;
}
public function reviewPendingDeclined($id){
   $data['action']                          = route('pending_organization_update_approve',$id);
    $data['action_name']                    = 'Edit';
    $data['pending_organization_user']      = $this->model->find($id);

    $data['name']                           = (is_null(old('name'))?$data['pending_organization_user']->name:old('name'));
    $data['id']                             = $data['pending_organization_user']->id;
    $data['contact_person']                 = (is_null(old('contact_person'))?$data['pending_organization_user']->contact_person:old('contact_person'));
    $data['position']                       = (is_null(old('position'))?$data['pending_organization_user']->position:old('position'));
    $data['url']                            = (is_null(old('url'))?$data['pending_organization_user']->url:old('url'));
    $data['contact_number']                 = (is_null(old('contact_number'))?$data['pending_organization_user']->contact_number:old('contact_number'));
    $data['email']                          = (is_null(old('email'))?$data['pending_organization_user']->email:old('email'));
    $data['password']                       = (is_null(old('password'))?$data['pending_organization_user']->password:old('password'));
    $data['status']                         = (is_null(old('status'))?$data['pending_organization_user']->status:old('status'));


    return $data;
}
    /*public function update(array $request, $id){
        $this->model->find($id)->update($request);
    }*/


    public function saveDeclined($request, $id){

        // $input = $request->all();
        // dd($input);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $user = $this->model->where('id',$id)->first();

        $user->status  = 'Declined';
        $user->save();

        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Declined Pending Organization";
        $activity->details                              = "Declined ".$user->name;
        $activity->org_id                               = 0;
        $activity->save();

        $this->updateOrganizationStatus('Declined',$id,$request);
        return ['status' => true, 'results' => 'Success'];
    }
    public function saveInactive($request, $id){

        // $input = $request->all();
        // dd($input);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user = $this->model->where('id',$id)->first();

        $user->status  = 'InActive';
        $user->save();
        #dd($id);
        DB::table('organizations')
            ->where('pending_organization_user_id', $id)
            ->update(['status' => 'InActive']);

        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Deactivated Organization";
        $activity->details                              = "Deactivated ".$user->name;
        $activity->org_id                               = 0;
        $activity->save();

        $this->updateOrganizationStatus('InActive',$id,$request);
        return ['status' => true, 'results' => 'Success'];
    }
    public function saveActive($request, $id){

        // $input = $request->all();
        // dd($input);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $user = $this->model->where('id',$id)->first();

        $user->status  = 'Active';
        $user->save();

         DB::table('organizations')
            ->where('id', $id)
            ->update(['status' => 'Active']);

        $org = Organization::where('pending_organization_user_id', $id)->first();

        $org->status = 'Active';
        $org->save();

        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Activated Organization";
        $activity->details                              = "Activated ".$org->name;
        $activity->org_id                               = 0;
        $activity->save();

        $request->activated = 'Activated';
        $this->updateOrganizationStatus('Active',$id,$request);
        return ['status' => true, 'results' => 'Success'];
    }
    public function saveApprove($request, $id){

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // $input = $request->all();
        // dd($input);
        $input      = $request->except(['_token','confirm']);
        $request->url = lcfirst(preg_replace('/\s+/', '-',(strtolower($input['url']))));
        $messages   = [
            'required' => 'The :attribute is required',
            'unique' => 'The :attribute is already taken. Please use another.'
        ];
        $validator  = Validator::make($input, [
            'name'     => 'required',
            'contact_person'    => 'required',
            'position'         => 'required',
            'url'              => 'required|unique:organizations,url',
            'contact_number'    => 'required|unique:organizations,contact_number',
            'email'            => 'required|unique:organizations,email'
        ], $messages);
            #dd($validator->fails());
        if($validator->fails()== true){
            return ['status' => false, 'results' => $validator];
        }
        $pending = $this->model->where('id',$id)->first();

        $pending->name                                      = $request->name;
        $pending->contact_person                            = $request->contact_person;
        $pending->position                                  = $request->position;
        $pending->url                                       = $request->url;
        $pending->contact_number                            = $request->contact_number;
        $pending->email                                     = $request->email;
        $pending->status  = 'Active';
        $pending->save();

        $org = new Organization;
        $org->name                                      = $request->name;
        $org->contact_person                            = $request->contact_person;
        $org->position                                  = $request->position;
        $org->url                                       = $request->url;
        $org->contact_number                            = $request->contact_number;
        $org->email                                     = $request->email;
        $org->pending_organization_user_id              = $pending->id;
        $org->password                                  = $pending->password;
        $org->status                                    = $pending->status;
        $org->save();

        $user = new User;
        $user->first_name                               = $request->contact_person;
        $user->email                                    = $request->email;
        $user->phone                                    = $request->contact_number;
        $user->password                                 = $pending->password;
        $user->organization_id                          = $org->id;
        $user->api_token                                = $org->id.str_random(60);
        $user->save();

        $assigned_user_role = new AssignedUserRole;
        $assigned_user_role->role_id                    = 2;
        $assigned_user_role->user_id                    = $user->id;
        $assigned_user_role->save();

        $role = new UserRole;
        $role->role_id                                  = 2;
        $role->user_id                                  = $user->id;
        $role->original_user_id                         = $user->id;
        $role->save();

        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Approved Pending Organization";
        $activity->details                              = "Approved ".$request->name;
        $activity->org_id                               = 0;
        $activity->save();

        $this->updateOrganizationStatus('Active',$id,$request);
        return ['status' => true, 'results' => 'Success'];
    }

    public function show($id){
        return $this->model->find($id);
    }


    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }

    public function updateOrganizationStatus($status,$id,$request)
    {
        #dd($status);
        $organization = $this->model->where('id',$id)->update([
                                    'status' => $status
                                ]);
        if($status == 'Active' && !isset($request->activated)){
            $status = 'Approved';
        }else if($status == 'Active' && isset($request->activated)){
            $status = 'Activated';
        }else if($status == 'InActive'){
            $status = 'Deactivated';
        }
        
        $get_organization = $this->model->where('id',$id)->first();
        Mail::send('cocard-church.email.organization_status',['get_organization' => $get_organization,'status' => $status, 'request' => $request], function ($m) use ($get_organization,$status) {
                $m->to(trim($get_organization->email), trim($get_organization->email))->subject($status.' Application for '. $get_organization->name);
        });
    }
}
