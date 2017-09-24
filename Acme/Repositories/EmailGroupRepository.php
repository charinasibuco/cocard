<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Acme\Helper\AesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Organization;
use Auth;

class EmailGroupRepository extends Repository{

    const LIMIT                 = 7;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    protected $listener;

    use AesTrait;

    public function model(){
        return 'App\EmailGroup';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function setDate($date){
        return date('Y-m-d', strtotime($date));
    }

    public function getEmailGroup($request, $slug)
    {
        $org = Organization::where('url', $slug)->first();
        $query = $this->model->where('organization_id', '=',  $org->id)
                             ->where('status', '=', 'Active');
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('name', 'LIKE', '%' . $search . '%')
                    ->paginate(self::LIMIT);
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('email_groups.*')
                     ->orderBy('email_groups.'.$order_by, $sort)
                     ->paginate();
    }

    public function create()
    {
        $data['action']                  = route('store_email_group');
        $data['action_name']             = 'Add';
        $data['name']                    = old('name');
        $data['details']                 = old('details');
        $data['id']                    ='';
        return $data;
    }

    public function save($request, $id = 0)
    {
        $action = ($id == 0) ? 'create_email_group' : 'edit_email_group';

        if($id == 0){

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];

            $validator  = Validator::make($input, [
                'name'              => 'required',
            ], $messages);

            if($validator->fails()){
                return ['status' => false, 'results' => $validator];
            } 

            $org = Organization::where('url', $request->slug)->first();

            $this->model->create([
                                    'organization_id' => $org->id,
                                    'name' => $input['name'], 
                                    'details' => $input['details']
                                ]); 
            
        }else{

            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];

            $validator  = Validator::make($input, [
                'name'              => 'required',
            ], $messages);

            $this->model->where('id',$id)->update([
                                    'name' => $input['name'], 
                                    'details' => $input['details'] 
                                ]);
        }

        return ['status' => true, 'results' => 'Success'];
    }

    public function edit($id){
        $data['action']               = route('update_email_group', $id);
        $data['action_name']          = 'Save';

        $email_group                  = $this->model->find($id);

        $data['name']                 = (is_null(old('name'))?$email_group->name:old('name'));
        $data['details']              = (is_null(old('details'))?$email_group->details:old('details'));
        $data['id']                     = $id;
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