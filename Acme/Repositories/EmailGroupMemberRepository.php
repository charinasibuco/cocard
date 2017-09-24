<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Acme\Helper\AesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Organization;
use App\User;
use Auth;
use DB;

class EmailGroupMemberRepository extends Repository{

    const LIMIT                 = 7;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    protected $listener;

    use AesTrait;

    public function model(){
        return 'App\EmailGroupMember';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function setDate($date){
        return date('Y-m-d', strtotime($date));
    }
    public function getEmailGroupMemberA($request, $id)
    {
        $query = $this->model->where('email_group_id', '=',  $id)
                             ->where('status', '=', 'Active')->get();
    }
    // for adding of new member in email group via filter tab
    public function getEmailGroupMemberFilter($request, $id)
    {
        $now = Carbon::now();
        $sub_years_from = $now->subYears($request->from);
        $from = Carbon::parse($sub_years_from)->format('Y-m-d');
        $sub_years_to = Carbon::now()->subYears($request->to)->subYear();
        $to = Carbon::parse($sub_years_to)->format('Y-m-d');
        $parse_now = Carbon::now()->format('Y-m-d');
        $search = trim($request->input('search'));
        $gender = trim($request->input('search_by_gender'));
        $age    = (trim($request->input('search_by_age')) == null)? 'All Age': (trim($request->input('search_by_age')));

        //pre-defined query of users to email group members
        $query = User::where('status','Active')
        ->whereNotIn('id',function($query_email_group_members) use ($id){
            $query_email_group_members
            ->select('user_id')
            ->from('email_group_members')
            ->where('status','Active')
            ->where('email_group_id',$id)
            ->get();
        })->where('organization_id',Auth::user()->organization_id)
            ->whereIn('id',function($query_members_only){
                $query_members_only
                ->select('user_id')
                ->from('assigned_user_roles')
                ->where('role_id',3)
                ->where('status','Active')
                ->get();
            });
           
        $search = trim($request->input('search'));
        $gender = trim($request->input('search_by_gender'));
       
        $marital_status =  trim($request->input('search_by_marital_status'));
        //if it has a search data for gender
        if($request->has('search_by_gender')){
        
            if($gender == 'All'){
                $query->where('gender','like','%male');
            }if($gender == 'Female'){
                $query->where('gender', 'Female');
            }if($gender == 'Male'){
                $query->where('gender', 'Male');
            }
            //search data for status
            if($request->has('search_by_marital_status')){
                $query->where(function($query) use($marital_status){
                    if($marital_status == 'All'){
                        $query->where('marital_status', 'Single')
                            ->orWhere('marital_status','Married')
                            ->orWhere('marital_status','Divorced')
                            ->orWhere('marital_status','Widowed/Widower')
                            ->orWhere('marital_status','Committed')
                            ->orWhere('marital_status','Not Specified');
                    }
                    else{
                        $query->where('marital_status', $marital_status);
                    }
                });
            }
            //search data for age
            if($request->has('from') || $request->has('to') ){

               $now = Carbon::now();
               $sub_years_from = $now->subYears($request->from);
               $from = Carbon::parse($sub_years_from)->format('Y-m-d');
               $sub_years_to = Carbon::now()->subYears($request->to)->subYear();
               $to = Carbon::parse($sub_years_to)->format('Y-m-d');
               $parse_now = Carbon::now()->format('Y-m-d');
                $age    = (trim($request->input('search_by_age')) == null)? 'All Age': (trim($request->input('search_by_age')));
               // dd($birthdate,$parse_now);

                $query->where(function($query) use ($age,$parse_now, $from, $to, $request){
                    if($request->from !== '' || $request->to !== ''){
                        $query->where('birthdate','<=', $from)
                        ->where('birthdate','>=', $to);
                        // $query->whereBetween('birthdate',[$from, $to]);
                    }
                    else{
                        $query->where('birthdate', '<=',  $parse_now);
                    }
                });
            }
        }
        return $query->get();
    }
    public function getEmailGroupMember($request, $id)
    {
        // dd($request->all());
        $query = $this->model->where('email_group_id', '=',  $id)
                             ->where('status', '=', 'Active');
        if ($request->has('search_by_gender')) {
             // if($request->has('search_by_marital_status')){
                 // if($request->has('search_by_age')){
                    $now = Carbon::now();
                    $sub_years_from = $now->subYears($request->from);
                    $from = Carbon::parse($sub_years_from)->format('Y-m-d');
                    $sub_years_to = Carbon::now()->subYears($request->to)->subYear();
                    $to = Carbon::parse($sub_years_to)->format('Y-m-d');
                    $parse_now = Carbon::now()->format('Y-m-d');
                    $search = trim($request->input('search'));
                    $gender = trim($request->input('search_by_gender'));
                    $age    = (trim($request->input('search_by_age')) == null)? 'All Age': (trim($request->input('search_by_age')));
                    //dd($from, $to);
                    // $gender = ['Male', 'Female'];
                    $marital_status =  trim($request->input('search_by_marital_status'));
                   // dd($birthdate,$parse_now);
                    $query = $query->where(function ($query) use ($search, $gender, $marital_status,$parse_now, $age, $from, $to, $request) {
                             $query->select('*')
                                ->where(function($query) use($gender){
                                    if($gender == 'All'){
                                        $query->where('gender', 'Male')
                                        ->orWhere('gender', 'Female');
                                }
                                else{
                                    $query->where('gender', $gender);
                                } 
                             })
                             // ->where('marital_status', $marital_status)
                                ->where(function($query) use($marital_status){
                                    if($marital_status == 'All'){
                                        $query->where('marital_status', 'Single')
                                        ->orWhere('marital_status','Married')
                                        ->orWhere('marital_status','Divorced')
                                        ->orWhere('marital_status','Widowed/Widower')
                                        ->orWhere('marital_status','Committed')
                                        ->orWhere('marital_status','Not Specified');
                                    }
                                    else{
                                        $query->where('marital_status', $marital_status);
                                    }
                                })
                                ->where(function($query) use ($age,$parse_now, $from, $to, $request){
                                    if($request->from !== '' || $request->to !== ''){
                                        $query->where('birthdate','<=', $from)
                                        ->where('birthdate','>=', $to);
                                        // $query->whereBetween('birthdate',[$from, $to]);
                                    }
                                    else{
                                        $query->where('birthdate', '<=',  $parse_now);
                                    }
                                })
                             // ->where('birthdate','>=', $birthdate)
                             // ->where('birthdate','<=', $sub_years->addYear())
                            ->paginate(self::LIMIT);
                            // dd($age);
                    });
                }
            // }
        // }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('email_group_members.*')
                     ->orderBy('email_group_members.'.$order_by, $sort)
                     ->paginate();
    }

    public function create()
    {
        $data['action']                  = route('store_email_group_member');
        $data['action_name']             = 'Add';

        $data['user_id']                 = 0;
        $data['name']                    = old('name');
        $data['email']                   = old('email');
        $data['gender']                  = old('gender');
        $data['marital_status']          = old('marital_status');
        $data['birthdate']               = old('birthdate');
        $data['id']                      ='';
        $data['mid']                     ='';
        $data['group_id']                     ='';
        return $data;
    }
     public function save_individual($email_group_id, $user_id)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user = \DB::table('users')->where('id',$user_id)->first();     
        //dd($user);
        $check_user = \DB::table('email_group_members')->where('user_id',$user_id)->where('email_group_id',$email_group_id)->first();

        if($check_user != null){
            return $this->model->where('user_id',$user_id)->update([
                                    'status' => 'Active'
                                ]);

        }
        return  $this->model->create([
                                    'email_group_id'    => $email_group_id,
                                    'user_id'           => $user->id,
                                    'name'              => $user->first_name.' '.$user->last_name, 
                                    'email'             => $user->email,
                                    'birthdate'         => $user->birthdate,
                                    'marital_status'    => $user->marital_status,
                                    'gender'            => $user->gender
                                ]);
    }
    public function save($request, $id = 0)
    {
        $action = ($id == 0) ? 'create_email_group_member' : 'edit_email_group_member';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if($id == 0){

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];
            $parse_birthday = Carbon::parse($input['birthdate'])->format('Y-m-d');
            // dd($parse_birthday);
            $validator  = Validator::make($input, [
                'name'              => 'required',
                'email'             => 'required|email',
            ], $messages);

            if($validator->fails()){
                return ['status' => false, 'results' => $validator];
            }

            $user = $this->model->where('user_id', '>', '1')->where('status', '=', 'Active');

            $query = $user->where('user_id', '=', $request->user_id)
                       ->where('email_group_id', '=', $request->email_group_id)->first();

            if($query == null){ 

                $this->model->create([
                                        'email_group_id'    => $input['email_group_id'],
                                        'user_id'           => $input['user_id'],
                                        'name'              => $input['name'], 
                                        'email'             => $input['email'],
                                        'birthdate'         => $parse_birthday,
                                        'marital_status'    => $input['marital_status'],
                                        'gender'            => $input['gender']
                                    ]);

                return ['status' => true, 'results' => 'Success'];

            }else{

            return ['status' => false, 'results' => 'Member is already in this Email Group'];

            } 
            
        }else{

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];
            $parse_birthday = Carbon::parse($input['birthdate'])->format('Y-m-d');
            $validator  = Validator::make($input, [
                'name'              => 'required',
                'email'             => 'required|email',
            ], $messages);

            $this->model->where('id',$id)->update([
                                    'name' => $input['name'], 
                                    'email' => $input['email'],
                                    'gender' => $input['gender'],
                                    'marital_status' => $input['marital_status'],
                                    'birthdate' => $parse_birthday
                                ]);

            return ['status' => true, 'results' => 'Success'];
        }

        
    }

    public function edit($id){
        $data['action']               = route('update_email_group_member', $id);
        $data['action_name']          = 'Edit';

        $email_group                  = $this->model->find($id);
        $parse_birthday               = Carbon::parse($email_group->birthdate)->format('m/d/y');
        $data['id']                     = $id;
        $data['mid']                     ='';
        
        $data['name']                 = (is_null(old('name'))?$email_group->name:old('name'));
        $data['email']                = (is_null(old('email'))?$email_group->email:old('email'));
        $data['user_id']              = (is_null(old('user_id'))?$email_group->user_id:old('user_id'));
        $data['gender']               = (is_null(old('gender'))?$email_group->gender:old('gender'));
        $data['marital_status']       = (is_null(old('marital_status'))?$email_group->marital_status:old('marital_status'));
        $data['birthdate']            = (is_null(old('birthdate'))?$parse_birthday:old('birthdate'));
        $data['email_group_id']       = (is_null(old('email_group_id'))?$email_group->email_group_id:old('email_group_id'));

        return $data;
    }

    public function softDelete($id){
        $this->model->where('id',$id)->update([
                                    'status' => 'InActive'
                                ]);

    }

    public function show($id){
        return $this->model->find($id);
    }

    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }

}