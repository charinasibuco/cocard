<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Acme\Helper\AesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Auth;
use DB;
use App\User;
use App\UserRole;
use App\Role;
use App\Family;
use App\Organization;
use App\AssignedUserRole;
use DateTime;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class FamilyMemberRepository extends Repository{

    const LIMIT                 = 7;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    protected $listener;

    use AesTrait;
    use Pagination;

    public function model(){
        return 'App\FamilyMember';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function setDate($date){
        return date('m-d-Y', strtotime($date));
    }
    
    //get family members of a specific family based on id which refers to the family id
    public function getFamilyMember($request, $id)
    {
        $this->SetPage($request);

        $query = $this->model->join('family', 'family.id', '=', 'family_members.family_id')
                            ->where('family_members.status', '=', 'Active')
                            ->where('family_members.family_id', '=', $id);
        if ($request->has(Constants::KEYWORD)) {
            $search = trim($request->input(Constants::KEYWORD));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }
        $order_by   = $this->SortBy;
        $sort       = $this->SortOrder;

        return $query->select('family_members.*')
                     ->orderBy('family_members.'.$order_by, $sort)
                     ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex);
    }
    public function create(){
    }

    //create family member form -setting values for each fields in form
    public function createfm($id){

        $data['action']                  = route('family_member_store', $id);
        $data['action_name']             = 'Add';

        $data['user_id']                 = 0;
        $data['first_name']              = old('first_name');
        $data['last_name']               = old('last_name');
        $data['middle_name']             = old('middle_name');
        $data['birthdate']               = old('birthdate');
        $data['gender']                  = old('gender');
        $data['allergies']               = old('allergies');
        $data['img']                     = old('img');
        $data['relationship']            = old('relationship');
        $data['first_name']              = old('first_name');
        $data['additional_info']         = old('additional_info');
        $data['child_number']            = old('child_number');

        return $data;
    }

    //adding new family member in a certain family
    public function save($request)
    {
        $action     = 'family_member_create';

        // $image_name = $this->upload($request);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $input      = $request->except(['_token','confirm']);
        $messages   = ['required' => 'The :attribute is required',];

        $validator  = Validator::make($input, [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'img'           => 'image|mimes:jpeg,png,jpg,gif,svg',
        ], $messages);

        // dd($request->all());

        if($validator->fails())
        {
            return ['status' => false,
                     'results' => $validator ,
                     Constants::ERROR_CODE => Constants::ERROR_CODE_FAMILY_MEMBER_VALIDATION,
                     ];
        }

        //getting family members with the same user_id and family_id entered by the user
        $user = $this->model;
        $query = $user->where('user_id', '=', $request->user_id)
                      ->where('user_id', '>', $request->user_id)
                      ->where('family_id', '=', $request->family_id)->first();

        //if there are no existing family members with same user_id and family_id
        if($query == null){
            $bdate= date("Y-m-d", strtotime($request->birthdate));
            $input['birthdate'] = $bdate;

            //if there's uploaded image
            if($request->hasFile('img'))
            {
                $imageName = time().'.'.$request->img->getClientOriginalExtension();
                $request->img->move(public_path('images'), $imageName);

                    $this->model->create([
                                        'family_id' => $input['family_id'],
                                        'user_id' => $input['user_id'],
                                        'first_name' => $input['first_name'],
                                        'last_name' => $input['last_name'],
                                        'middle_name' => $input['middle_name'],
                                        'birthdate' => $bdate,
                                        'gender' => $input['gender'],
                                        'allergies' => $input['allergies'],
                                        'img' => $imageName,
                                        'relationship' => $input['relationship'],
                                        'additional_info' => $input['additional_info'],
                                        'child_number' => $input['child_number'],
                                    ]);
            //if there's no uploaded image
            }else{
                    $this->model->create([
                                        'family_id' => $input['family_id'],
                                        'user_id' => $input['user_id'],
                                        'first_name' => $input['first_name'],
                                        'last_name' => $input['last_name'],
                                        'middle_name' => $input['middle_name'],
                                        'birthdate' => $bdate,
                                        'gender' => $input['gender'],
                                        'allergies' => $input['allergies'],
                                        'img' => (isset($input['img']) ? $input['img'] : ''),
                                        'relationship' => $input['relationship'],
                                        'additional_info' => $input['additional_info'],
                                        'child_number' => $input['child_number'],
                                    ]);
            }

            return ['status' => true, 'results' => 'Success'];

        //if there are existing family members with the same user_id and family_id
        }else{

            return ['status' => false, 'results' =>
                     'Member is already in this Family',
                     Constants::ERROR_CODE => Constants::ERROR_CODE_FAMILY_MEMBER_EXIST,
                     "data" => $query
                     ];
        }
    }

    //adding new family member and at the same time adding/registering this member to the member's directory (USER DASHBOARD)
    public function saveUser($request)
    {
        $action     = 'family_member_create';

        // $image_name = $this->upload($request);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $input      = $request->except(['_token','confirm']);
        $messages   = ['required' => 'The :attribute is required',
                        'email.unique_custom' => 'Email Address is already taken. Please choose another.',
                        'phone.unique_custom' => 'Phone Number is already taken. Please choose another.',];

        //unique custom validation query can be found in C:\laragon\www\cocard-church\app\Providers\AppServiceProvider.php:
        //refer to this file to understand unqiue_custom validation
        $validator  = Validator::make($input, [
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required|unique_custom:users,email,organization_id,'.$request->organization_id.',status,'.'Active',
            'phone'                 => 'required|unique_custom:users,phone,organization_id,'.$request->organization_id.',status,'.'Active',
            'password'              => 'required',
            'img'           => 'image|mimes:jpeg,png,jpg,gif,svg',
        ], $messages);

        // dd($request->all());

        if($validator->fails())
        {
            return [
                    'status' => false, 
                    'results' => $validator,
                    Constants::ERROR_CODE => Constants::ERROR_CODE_FAMILY_MEMBER_USER_VALIDATION
                ];
        }


            $bdate= date("Y-m-d", strtotime($request->birthdate));
            $input['birthdate'] = $bdate;
            $input['api_token'] = str_random(60);
            $imageName = NULL;

            if($request->hasFile('img'))
            {
                $imageName = time().'.'.$request->img->getClientOriginalExtension();
                $request->img->move(public_path('images'), $imageName);
            }

            //save family member to user's table as a member in organization
            $user  = User::create([
                                    'organization_id' => $input['organization_id'],
                                    'first_name' => $input['first_name'],
                                    'last_name' => $input['last_name'],
                                    'middle_name' => $input['middle_name'],
                                    'birthdate' => $bdate,
                                    'gender' => $input['gender'],
                                    'image' => $imageName,
                                    'email' => $input['email'],
                                    'phone' => $input['phone'],
                                    'password' => bcrypt($input['password']),
                                    'api_token' => $input['api_token']
                                ]);

            User::where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
            //adding member role to this user
            AssignedUserRole::create(['role_id' => 3 , 'user_id' => $user->id]);
            //assigning this role as the default role when uer logs in
            UserRole::create(['role_id' => 3 , 'user_id' => $user->id, 'original_user_id' => $user->id]);
            

            if($request->hasFile('img'))
            {

                    $this->model->create([
                                        'family_id' => $input['family_id'],
                                        'user_id' => $user->id,
                                        'first_name' => $input['first_name'],
                                        'last_name' => $input['last_name'],
                                        'middle_name' => $input['middle_name'],
                                        'birthdate' => $bdate,
                                        'gender' => $input['gender'],
                                        'allergies' => $input['allergies'],
                                        'img' => $imageName,
                                        'relationship' => $input['relationship'],
                                        'additional_info' => $input['additional_info'],
                                        'child_number' => $input['child_number'],
                                    ]);
            }else{
                    $this->model->create([
                                        'family_id' => $input['family_id'],
                                        'user_id' => $user->id,
                                        'first_name' => $input['first_name'],
                                        'last_name' => $input['last_name'],
                                        'middle_name' => $input['middle_name'],
                                        'birthdate' => $bdate,
                                        'gender' => $input['gender'],
                                        'allergies' => $input['allergies'],
                                        'img' => (isset($input['img']) ? $input['img'] : ''),
                                        'relationship' => $input['relationship'],
                                        'additional_info' => $input['additional_info'],
                                        'child_number' => $input['child_number'],
                                    ]);
            }

            return ['status' => true, 'results' => 'Success'];
    }

    //save existing family member to user's table as a member (USER DASHBOARD)
    public function saveMemberToUser($request, $id)
    {
        $action     = 'family_member_create';

        // $image_name = $this->upload($request);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $input      = $request->except(['_token','confirm']);
        $messages   = ['required' => 'The :attribute is required',
                        'email.unique_custom' => 'Email Address is already taken. Please choose another.',
                        'phone.unique_custom' => 'Phone Number is already taken. Please choose another.',];

        $validator  = Validator::make($input, [
            'email'                 => 'required|unique_custom:users,email,organization_id,'.$request->organization_id.',status,'.'Active',
            'phone'                 => 'required|unique_custom:users,phone,organization_id,'.$request->organization_id.',status,'.'Active',
            'password'              => 'required'
        ], $messages);

        // dd($request->all());

        if($validator->fails())
        {
            return ['status' => false, 'results' => $validator];
        }


            $bdate= date("Y-m-d", strtotime($request->birthdate));
            $input['birthdate'] = $bdate;
            $input['api_token'] = str_random(60);

            $user  = User::create([
                                    'organization_id' => $input['organization_id'],
                                    'first_name' => $input['first_name'],
                                    'last_name' => $input['last_name'],
                                    'middle_name' => $input['middle_name'],
                                    'birthdate' => $bdate,
                                    'gender' => $input['gender'],
                                    'image' => $input['image'],
                                    'email' => $input['email'],
                                    'phone' => $input['phone'],
                                    'password' => bcrypt($input['password']),
                                    'api_token' => $input['api_token']
                                ]);

            User::where('id',$user->id)->update(['api_token' => $user->id.'_'.str_random(60)]);
            AssignedUserRole::create(['role_id' => 3 , 'user_id' => $user->id]);
            UserRole::create(['role_id' => 3 , 'user_id' => $user->id, 'original_user_id' => $user->id]);

            $this->model->where('id', $id)->update([ 'user_id' => $user->id ]);

            

            return ['status' => true, 'results' => 'Success'];
    }

    //save multiple family members to table (ADMIN DASHBOARD)
    public function saveArray($request)
    {
        $input = $request->all();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // dd($input);
        //if request has only one family member to be stored in table
        if(count($request['item']) == 1){

            foreach($request['item'] as $row) {

                $query = $this->model->where('user_id', '=', $row['user_id'])
                                    ->where('family_id', '=', $row['family_id'])
                                    ->where('status', '=', 'Active')
                                    ->first();

                                    // dd($query);

                if($query == null || $row['user_id'] == 0){

                    if($row['img'])
                    {
                        $imageName = time().'.'.$row['img']->getClientOriginalExtension();
                        $row['img']->move(public_path('images'), $imageName);
                        $row['img'] = $imageName;
                    }
                    if($row['birthdate'])
                    {
                        $bdate= date("Y-m-d", strtotime($row['birthdate']));
                    }
                     // dd('false');
                    $this->model->create([
                                            'family_id' => $row['family_id'],
                                            'user_id' => $row['user_id'],
                                            'first_name' => $row['first_name'],
                                            'middle_name' => $row['middle_name'],
                                            'last_name' => $row['last_name'],
                                            'birthdate' => (isset($bdate) ? $bdate : 0000-00-00),
                                            'gender' => $row['gender'],
                                            'img' => $row['img'],
                                            'relationship' => $row['relationship'],
                                            'additional_info' => $row['additional_info'],
                                            'child_number' => $row['child_number'],
                                         ]);

                    $validates = ['status' => true, 'results' => 'Success'];

                }else{

                    $validates = ['status' => false, 'results' => $row['first_name']." ". $row['last_name']." is already assigned to this family."];
                }
            }
        //if request has multiple family member to be stored
        }else{
            //saving each family member as per item number
            foreach($request['item'] as $row) {

                //validating entered user if existing in family member's table with same user_id and family_id
                $query = $this->model->where('user_id', '=', $row['user_id'])
                                    ->where('family_id', '=', $row['family_id'])
                                    ->where('status', '=', 'Active')
                                    ->first();

                                    // dd($query);
                //if user is not existing and if user_id is 0
                if($query == null || $row['user_id'] == 0){

                    if($row['img'])
                    {
                        $imageName = time().'.'.$row['img']->getClientOriginalExtension();
                        $row['img']->move(public_path('images'), $imageName);
                        $row['img'] = $imageName;
                    }
                    if($row['birthdate'])
                    {
                        $row['birthdate']= date("Y-m-d", strtotime($row['birthdate']));
                    }
                     // dd('false');
                    $this->model->create([
                                            'family_id' => $row['family_id'],
                                            'user_id' => $row['user_id'],
                                            'first_name' => $row['first_name'],
                                            'middle_name' => $row['middle_name'],
                                            'last_name' => $row['last_name'],
                                            'birthdate' => $row['birthdate'],
                                            'gender' => $row['gender'],
                                            'img' => $row['img'],
                                            'relationship' => $row['relationship'],
                                            'additional_info' => $row['additional_info'],
                                            'child_number' => $row['child_number'],
                                         ]);

                    $validates = ['status' => true, 'results' => 'Success'];
                //if there is existing user
                }else{
                    $validates = ['status' => false, 'results' => "Member/s are already assigned to this family."];
                }
            }
        }

        return $validates;

    }

    //assigning member to a family (ADMIN DASHBOARD MEMBER'S DIRECTORY)
    public function assignFamily(Request $request, $id)
    {

        //validating is user is existing in family entered
        $user = User::where('id', $id)->first();
        $family = Family::where('id', $request->family_id)->first();

        $query = $this->model->where('user_id', '=', $user->id)
                             ->where('family_id', '=', $family->id)
                             ->where('status', '=', 'Active')
                             ->first();
        //if user is not existing
        if($query == null){

            $this->model->create([
                                        'family_id' => $request->family_id,
                                        'user_id' => $id,
                                        'first_name' => $user->first_name,
                                        'last_name' => $user->last_name,
                                        'middle_name' => $user->middle_name,
                                        'birthdate' => $user->birthdate,
                                        'gender' => $user->gender,
                                        'img' => (is_null($user->image)?'':$user->image),
                                    ]);

            return ['status' => true, 'results' => 'Success'];
        //if user exists
        }else{

            return ['status' => false, 'results' => 'Fail'];
        }

    }

    //assigning value to each field in edit family member's form 
    public function edit($id){
        $data['action']               = route('family_member_update', $id);
        $data['action_name']          = 'Edit';

        $family                       = $this->model->find($id);

        $data['user_id']              = (is_null(old('user_id'))?$family->user_id:old('user_id'));
        $data['family_id']            = (is_null(old('family_id'))?$family->family_id:old('family_id'));
        $data['first_name']           = (is_null(old('first_name'))?$family->first_name:old('first_name'));
        $data['last_name']            = (is_null(old('last_name'))?$family->last_name:old('last_name'));
        $data['middle_name']          = (is_null(old('middle_name'))?$family->middle_name:old('middle_name'));
        $data['birthdate']            = (is_null(old('birthdate'))?$family->format_birthdate->format("m/d/Y"):old('birthdate'));
        $data['gender']               = (is_null(old('gender'))?$family->gender:old('gender'));
        $data['allergies']            = (is_null(old('allergies'))?$family->allergies:old('allergies'));
        $data['img']                  = (is_null(old('img'))?$family->img:old('img'));
        $data['relationship']         = (is_null(old('relationship'))?$family->relationship:old('relationship'));
        $data['additional_info']      = (is_null(old('additional_info'))?$family->additional_info:old('additional_info'));
        $data['child_number']         = (is_null(old('child_number'))?$family->child_number:old('child_number'));

        return $data;


    }

    //updates family member information 
    public function updates($request, $id)
    {
        $action     = 'family_member_edit';

        $input      = $request->except(['_token','confirm']);
        $messages   = ['required' => 'The :attribute is required',];

        $validator  = Validator::make($input, [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'img'           => 'image|mimes:jpeg,png,jpg,gif,svg',
        ], $messages);

        // dd($request->all());

        if($validator->fails())
        {
            return ['status' => false, 'results' => $validator];
        }
        $bdate= date("Y-m-d", strtotime($request->birthdate));
        $input['birthdate'] = $bdate;
        if($request->hasFile('img'))
        {
            $imageName = time().'.'.$request->img->getClientOriginalExtension();
            $request->img->move(public_path('images'), $imageName);

                $this->model->where('id',$id)->update([
                                                        'first_name' => $input['first_name'],
                                                        'last_name' => $input['last_name'],
                                                        'middle_name' => $input['middle_name'],
                                                        'birthdate' => $bdate,
                                                        'gender' => $input['gender'],
                                                        'allergies' => $input['allergies'],
                                                        'img' => $imageName,
                                                        'relationship' => $input['relationship'],
                                                        'additional_info' => $input['additional_info'],
                                                        'child_number' => $input['child_number'],
                                                    ]);
        }else{
                $this->model->where('id',$id)->update([
                                                        'first_name' => $input['first_name'],
                                                        'last_name' => $input['last_name'],
                                                        'middle_name' => $input['middle_name'],
                                                        'birthdate' => $bdate,
                                                        'gender' => $input['gender'],
                                                        'allergies' => $input['allergies'],
                                                        'relationship' => $input['relationship'],
                                                        'additional_info' => $input['additional_info'],
                                                        'child_number' => $input['child_number'],
                                ]);
        }

        return ['status' => true, 'results' => 'Success'];
    }

    //AJAX search user
    public function autoComplete(Request $request, $slug)
    {
        $term = $request->get('term','');
        $org = Organization::where('url', $slug)->first();
        $query = User::where('organization_id', '=', $org->id)->where('users.status', '=', 'Active')->join('assigned_user_roles', 'assigned_user_roles.user_id', '=', 'users.id')->where('assigned_user_roles.role_id', '3');

        $users = $query->where(function ($query) use ($term) {
                     $query->where('first_name', 'LIKE', '%' . $term . '%')
                     ->orWhere('last_name', 'LIKE', '%' . $term . '%')
                     ->orWhere('middle_name', 'LIKE', '%' . $term . '%');
            })->get();

        $data=array();
        foreach ($users as $user) {
            $bdate= date("m/d/Y", strtotime($user->birthdate));
            $new_bdate = new DateTime($bdate);
            $now  = new DateTime('today');
            // assigning user's info to each attributes
                $data[]=array('label'=>$user->first_name.' '.$user->middle_name.' '.$user->last_name,
                                'id'=>$user->id,
                                'first_name'=>$user->first_name,
                                'last_name'=>$user->last_name,
                                'middle_name'=>$user->middle_name,
                                'birthdate'=>date("n/j/Y", strtotime($user->birthdate)),
                                'gender'=>$user->gender,
                                'marital_status' =>$user->marital_status,
                                'img'=>$user->image,
                                'email'=>$user->email,
                                'age' =>$new_bdate->diff($now)->y);

        }
        //showing results from entering user
        if(count($data))
              return $data;
        else
            return ['label'=>'No Result Found','id'=>''];

    }

    //delete family member
    public function softDelete($id){
        $this->model->where('id',$id)->update(['status' => 'InActive']);
    }

    public function show($id){
        return $this->model->find($id);
    }

    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }

}
