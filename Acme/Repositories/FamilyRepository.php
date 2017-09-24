<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Acme\Helper\AesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Organization;
use App\FamilyMember;
use App\User;
use Auth;
use Acme\Helper\Api;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class FamilyRepository extends Repository{

    const LIMIT                 = 7;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    protected $listener;

    use AesTrait;
    use Pagination;

    public function model(){
        return 'App\Family';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function setDate($date){
        return date('Y-m-d', strtotime($date));
    }

    public function findFamily($id){
        return $this->model->find($id);
    }

    //get family for each organizations
    public function getFamily($request, $slug)
    {
        $org = Organization::where('url', $slug)->first();
        $query = $this->model->where('family.organization_id', '=',  $org->id)
                             ->where('family.status', '=', 'Active');
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('name', 'LIKE', '%' . $search . '%')
                    ->paginate(self::LIMIT);
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('family.*')
                     ->orderBy('family.'.$order_by, $sort)
                     ->paginate();
    }
    //get church-goer's family
    public function getUserFamily($request)
    {  

        $this->SetPage($request);
        // $familymember = $this->model->where('user_id', '=', Auth::user()->id)->first();
        // $family_id  = $familymember->family_id;

        $query = $this->model->join('family_members', 'family_members.family_id', '=', 'family.id')
                            ->where('family_members.user_id', '=',  Api::getUserByMiddleware()->id)
                            ->where('family.status', '=', 'Active')
                            ->where('family_members.status', '=', 'Active');
        if ($request->has(Constants::KEYWORD)) {
            $search = trim($request->input(Constants::KEYWORD));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        $order_by   = $this->SortBy;
        $sort       = $this->SortOrder;

        return $query->select('family.*')
                     ->orderBy('family.'.$order_by, $sort)
                     ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex);
    }
    //assigning values to each fields of family create form
    public function create()
    {
        $data['action']                  = route('family_store');
        $data['action_name']             = 'Add';
        $data['name']                    = old('name');
        $data['description']             = old('description');
        $data['primary_phone']           = old('primary_phone');
        $data['secondary_phone']         = old('secondary_phone');
        $data['primary_email']           = old('primary_email');
        $data['secondary_email']         = old('secondary_email');
        $data['address_1']               = old('address_1');
        $data['address_2']               = old('address_2');
        $data['city']                    = old('city');
        $data['state']                   = old('state');
        $data['zipcode']                 = old('zipcode');
        return $data;
    }
    //adding/updating family in database
    public function save($request, $id = 0)
    {
        $action = ($id == 0) ? 'family_create' : 'family_edit';
        //if id is 0 system adds family to database
        if($id == 0){

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];

            
            $validator  = Validator::make($input, [
                'name'              => 'required',
                'primary_phone'     => 'required',
                'primary_email'     => 'required',
            ], $messages);

            if($validator->fails()){
                return ['status' => false,
                        'results' => $validator];
            } 

                $org = Organization::where('url', $request->slug)->first();

                $this->model->create([
                                    'organization_id' => $org->id,
                                    'name' => $input['name'], 
                                    'description' => $input['description'],
                                    'primary_phone' => $input['primary_phone'],
                                    'secondary_phone' => $input['secondary_phone'],
                                    'primary_email' => $input['primary_email'],
                                    'secondary_email' => $input['secondary_email'],
                                    'address_1' => $input['address_1'],
                                    'address_2' => $input['address_2'],
                                    'city' => $input['city'],
                                    'state' => $input['state'],
                                    'zipcode' => $input['zipcode']  
                                ]);
        //if id is not equal to zero, family member is updated to new info entered  
        }else{

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];
            
            $validator  = Validator::make($input, [
                'name'              => 'required',
                'primary_phone'     => 'required',
                'primary_email'     => 'required',
            ], $messages);

            if($validator->fails()){
                return ['status' => false, 
                        'results' => $validator, 
                        'input' => $input,
                        'action' => $action]
                        ;
            } 
            
            $this->model->where('id',$id)->update([
                                    'name' => $input['name'], 
                                    'description' => $input['description'],
                                    'primary_phone' => $input['primary_phone'],
                                    'secondary_phone' => $input['secondary_phone'],
                                    'primary_email' => $input['primary_email'],
                                    'secondary_email' => $input['secondary_email'],
                                    'address_1' => $input['address_1'],
                                    'address_2' => $input['address_2'],
                                    'city' => $input['city'],
                                    'state' => $input['state'],
                                    'zipcode' => $input['zipcode'] 
                                ]);
            
        }

        return [
                'status' => true,
                'results' => 'Success',
                 'input' => $input,
                 'action' => $action
               ];
    }

    //if church-goer adds new family it saves the family and his/her info as a family member to that specific family
    public function user_save($request)
    {

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];

            $validator  = Validator::make($input, [
                'name'              => 'required',
                'primary_phone'     => 'required',
                'primary_email'     => 'required',
            ], $messages);

            if($validator->fails()){
                return ['status' => false,
                        'results' => $validator,
                        'input' => $input
                        ];
            } 

                $org = Organization::where('url', $request->slug)->first();
                //adds new family
                $family = $this->model->create([
                                    'organization_id' => $org->id,
                                    'name' => $input['name'], 
                                    'description' => $input['description'],
                                    'primary_phone' => $input['primary_phone'],
                                    'secondary_phone' => $input['secondary_phone'],
                                    'primary_email' => $input['primary_email'],
                                    'secondary_email' => $input['secondary_email'],
                                    'address_1' => $input['address_1'],
                                    'address_2' => $input['address_2'],
                                    'city' => $input['city'],
                                    'state' => $input['state'],
                                    'zipcode' => $input['zipcode']  
                                ]);

                $user = User::where('id', Api::getUserByMiddleware()->id)->first();
                //adds church goer as family member to that specific family
                FamilyMember::create([
                                        'family_id' => $family->id,
                                        'user_id' => $user->id,
                                        'first_name' => $user->first_name,
                                        'last_name' => $user->last_name,
                                        'middle_name' => $user->middle_name,
                                        'birthdate' => $user->birthdate,
                                        'gender' => $user->gender,
                                        'img' => (is_null($user->image)?'':$user->image),
                                    ]);

            

        return ['status' => true,
             'results' => 'Success',
             'input' => $input
        ];
    }
    //assigning value to each fields of family edit form
    public function edit($id){
        $data['action']               = route('family_update', $id);
        $data['action_name']          = 'Edit';

        $family                       = $this->model->find($id);

        $data['family_id']            = (is_null(old('family_id'))?$family->family_id:old('family_id'));
        $data['name']                 = (is_null(old('name'))?$family->name:old('name'));
        $data['description']          = (is_null(old('description'))?$family->description:old('description'));
        $data['primary_phone']        = (is_null(old('primary_phone'))?$family->primary_phone:old('primary_phone'));
        $data['secondary_phone']      = (is_null(old('secondary_phone'))?$family->secondary_phone:old('secondary_phone'));
        $data['primary_email']        = (is_null(old('primary_email'))?$family->primary_email:old('primary_email'));
        $data['secondary_email']      = (is_null(old('secondary_email'))?$family->secondary_email:old('secondary_email'));
        $data['address_1']            = (is_null(old('address_1'))?$family->address_1:old('address_1'));
        $data['address_2']            = (is_null(old('address_2'))?$family->address_2:old('address_2'));
        $data['city']                 = (is_null(old('city'))?$family->city:old('city'));
        $data['state']                = (is_null(old('state'))?$family->state:old('state'));
        $data['zipcode']              = (is_null(old('zipcode'))?$family->zipcode:old('zipcode'));

        return $data;
    }
    //delete family
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