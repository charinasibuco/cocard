<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\DonationList;
use App\DonationCategory;
use DB;
use Auth;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class DonationListRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    use Pagination;

    protected $listener;

    public function model(){
        return 'App\DonationList';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function getUserDonationList()
    {
        // dd($request);
        $query = $this->model->get();
        return $query;
    }


    public function getDonationList($request,$id)
    {
        // dd($request);
        $this->SetPage($request);
        $query = $this->model->join('donation_category','donation_list.donation_category_id','=','donation_category.id')
                            ->where('donation_list.status','Active')
                            ->where('donation_category.organization_id',$id);
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('donation_list.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('donation_list.description', 'LIKE', '%' . $search . '%');
            });
        }

        $order_by   = $this->SortBy;
        $sort       = $this->SortOrder;

        return $query->select('donation_list.*')
            ->orderBy('donation_list.'.$order_by, $sort)
             ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                     Constants::PAGE_INDEX,
                     $this->PageIndex);
    }
    public function getDonationListPerOrg($request,$orgid,$recurring)
    {
        //onetime
        if($recurring ==0){
            $query = $this->model->join('donation_category', 'donation_category.id', '=', 'donation_list.donation_category_id')
                            ->where('donation_category.organization_id', '=', $orgid);
        }
        //recurring
        else{
            $query = $this->model->join('donation_category', 'donation_category.id', '=', 'donation_list.donation_category_id')
                            ->where('donation_category.organization_id', '=', $orgid)
                            ->where('donation_list.recurring', '=', $recurring);
        }
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('donation_list.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('donation_list.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('donation_list.status', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('donation_list.*')
            ->orderBy('donation_list.'.$order_by, $sort)
            ->paginate();
    }

    public function create(){

        $data['action']                = route('store_donation_list');
        $data['action_name']           = 'Add';
        $data['donation_category_id']  = 'donation_category_id';


        return $data;
    }

    public function save($request, $id = 0){
        $action     = ($id == 0) ? 'store_donation_list' : 'update_donation_list';

        $input      = $request->except(['_token','confirm']);
        $messages   = [
            'required' => 'The :attribute is required',
        ];
        $validator  = Validator::make($input, [
            'donation_category_id'  => 'required',
            'name'                  => 'required',
            'description'           => 'required',
            'recurring'             => 'required',
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
            $input      = $request->except(['_token','confirm','slug']);
            $this->model->where('id',$id)->update($input);
           #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        return ['status' => true, 'results' => 'Success'];

    }

    public function edit($id){
        $data['action']         = route('update_donation_list', $id);
        $data['action_name']    = 'Edit';
        $data['donation_list']      = $this->model->find($id);

        $data['name']          = (is_null(old('name'))?$data['donation_list']->name:old('name'));
        $data['donation_category_id']          = (is_null(old('donation_category_id'))?$data['donation_list']->name:old('donation_category_id'));
        $data['description']    = (is_null(old('description'))?$data['donation_list']->description:old('description'));
        $data['donation_category_id']    = (is_null(old('donation_category_id'))?$data['donation_list']->donation_category_id:old('donation_category_id'));
        $data['recurring']    = (is_null(old('recurring'))?$data['donation_list']->recurring:old('recurring'));
        $data['status']         = (is_null(old('status'))?$data['donation_list']->status:old('status'));

        return $data;
    }

    /*public function update(array $request, $id){
        $this->model->find($id)->update($request);
    }*/

    public function show($id){
        return $this->model->find($id);
    }
    public function delete($id){
        $user = $this->model->where('id',$id)->first();

        $user->status  = 'InActive';
        $user->save();
    }

    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }
}
