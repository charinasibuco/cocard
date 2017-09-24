<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\DonationCategory;
use App\DonationList;

class DonationCategoryRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';
  /**/

    protected $listener;

    public function model(){
        return 'App\DonationCategory';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }
     public function findDonationCategory($id){
        return $this->model->find($id);
    }

    public function getUserDonationCategory()
    {
        #dd($request->id);
        $query = $this->model->get();

        return $query;
    }
    public function getDonationCategory($request,$id)
    {
        #dd($request->id);
        $query = $this->model->where('status','Active')->where('organization_id',$id);
        
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->paginate(7);
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'name';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'asc';

        return $query->select('donation_category.*')
            ->orderBy('donation_category.'.$order_by, $sort)
            ->paginate(7);
    }

    public function create(){

        $data['action']                = route('store_donation_category');
        $data['action_name']           = 'Add';
        $data['title']                 = old('title');
        $data['description']           = old('description');
        $data['status']                = old('status');
        $data['id']                    ='';

        return $data;
    }

    public function save($request, $id = 0){
        $action     = ($id == 0) ? 'store_donation_category' : 'update_donation_category';

        $input      = $request->except(['_token','confirm']);
        $messages   = [
            'required' => 'The :attribute is required',
        ];
        $validator  = Validator::make($input, [
            'name'                  => 'required',
            'description'           => 'required',
        ], $messages);
        #dd($validator->fails());

        if($validator->fails()== true){
            #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        if($id == 0){
            $this->model->create($input);
            #$this->listener->setMessage('User is successfully created!');
        }else{
            $input      = $request->except(['_token','confirm','slug','cb_val','dcid']);
            $this->model->where('id',$id)->update($input);
           #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        return ['status' => true, 'results' => 'Success'];

    }

    public function edit($id){
        $data['action']                 = route('update_donation_category', $id);
        $data['action_name']            = 'Edit';
        $data['donation_list']          = $this->model->find($id);

        $data['name']                   = (is_null(old('name'))?$data['donation_list']->name:old('name'));
        $data['description']            = (is_null(old('description'))?$data['donation_list']->description:old('description'));
        $data['status']                 = (is_null(old('status'))?$data['donation_list']->status:old('status'));
        $data['id']                     = $id;

        return $data;
    }

    /*public function update(array $request, $id){
        $this->model->find($id)->update($request);
    }*/

    public function show($id){
        return $this->model->find($id);
    }


    public function delete($id){
        #$this->model->where('id',$id)->delete();
        $user = $this->model->where('id',$id)->first();

        $user->status  = 'InActive';
        $user->save();
    }
}
