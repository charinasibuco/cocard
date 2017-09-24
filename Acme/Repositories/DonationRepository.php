<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Donation;
use App\Transaction;
use App\TransactionDetails;
use Auth;
use DB;
use Acme\Repositories\Cart\EasyCart;
use Acme\Repositories\Cart\DonationItem;
use Acme\Helper\Api;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class DonationRepository extends Repository{

    use Pagination;

    protected $listener;

    public function model(){
        return 'App\Donation';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function getDonation($request)
    {
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('start_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('end_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('amount', 'LIKE', '%' . $search . '%')
                    ->orWhere('user_id', 'LIKE', '%' . $search . '%')
                    ->orWhere(' donation_category_id', 'LIKE', '%' . $search . '%')
                    ->orWhere(' frequency_id', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('donation.*')
            ->orderBy('donation.'.$order_by, $sort)
            ->paginate();
    }

    public function getTransactionDonation($request)
    {

        $query = $this->model->join('transaction', 'transaction.id', '=', 'donation.transaction_id')
                             ->where('transaction.user_id', '=', Api::getUserByMiddleware()->id);

        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('start_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('end_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('amount', 'LIKE', '%' . $search . '%')
                    ->orWhere('user_id', 'LIKE', '%' . $search . '%')
                    ->orWhere(' donation_category_id', 'LIKE', '%' . $search . '%')
                    ->orWhere(' frequency_id', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('donation.*')
            ->orderBy('donation.'.$order_by, $sort)
            ->paginate();

    }

    public function getUserDonation($request)
    {
        $this->SetPage($request);
        $user = $request['id'];
        $query = $this->model->leftjoin('transaction','transaction.id','=','donation.transaction_id')
                            ->leftjoin('frequency','frequency.id','=','donation.frequency_id')
                            ->leftjoin('donation_list','donation_list.id','=','donation.donation_list_id')
                            ->leftjoin('donation_category','donation_category.id','=','donation_list.donation_category_id')
                            ->where('transaction.user_id', $user);

        $order_by   = $this->SortBy;
        $sort       = $this->SortOrder;

        if ($request->has(DataFields::DONATION_TYPE)) {

            if($request->donation_type == Constants::ALL){

                $query = $query->select('donation.id as id', 'donation.donation_type as Type', 'donation_category.name as Category', 'donation_list.name as Name', 'frequency.title as Frequency', 'donation.amount as Amount', 'donation.created_at as Date', 'donation.status as status', 'donation.donation_list_id as DonationListId')
                 ->where('donation_list_id', '!=', 0)
                 ->orderBy('donation.created_at','desc');

            }else{

                $donation_type = trim($request->input(DataFields::DONATION_TYPE));
                $query = $query->where(function ($query) use ($donation_type) {
                         $query->where('donation.donation_type', 'LIKE', '%' . $donation_type . '%');
                });
            }
        }

        return $query->select('donation.id as id', 'donation.donation_type as Type', 'donation_category.name as Category', 'donation_list.name as Name', 'frequency.title as Frequency', 'donation.amount as Amount', 'donation.created_at as Date', 'donation.status as status', 'donation.donation_list_id as DonationListId')
                    ->where('donation_list_id', '!=', 0)
                    ->orderBy('donation.created_at','desc')
                    ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex)
                    ->setPath('?'.DataFields::DONATION_TYPE.'='.$request->donation_type);
    }
    public function getUserDonationAll($request)
    {
        $user = $request['id'];
        $query = $this->model->leftjoin('transaction','transaction.id','=','donation.transaction_id')
                            ->leftjoin('frequency','frequency.id','=','donation.frequency_id')
                            ->leftjoin('donation_list','donation_list.id','=','donation.donation_list_id')
                            ->leftjoin('donation_category','donation_category.id','=','donation_list.donation_category_id')
                            ->where('transaction.user_id', $user);

        if ($request->has('donation_type')) {

            if($request->donation_type == "All"){

                $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
                $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

                return $query->select('donation.id as id', 'donation.donation_type as Type', 'donation_category.name as Category', 'donation_list.name as Name', 'frequency.title as Frequency', 'donation.amount as Amount', 'donation.created_at as Date', 'donation.status as status', 'donation.donation_list_id as DonationListId')
                 ->where('donation_list_id', '!=', 0)
                ->orderBy('donation.created_at','desc')
                ->get();

            }else{

                $donation_type = trim($request->input('donation_type'));
                $query = $query->where(function ($query) use ($donation_type) {
                         $query->where('donation.donation_type', 'LIKE', '%' . $donation_type . '%')
                               ->get();
                });
            }
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('donation.id as id', 'donation.donation_type as Type', 'donation_category.name as Category', 'donation_list.name as Name', 'frequency.title as Frequency', 'donation.amount as Amount', 'donation.created_at as Date', 'donation.status as status', 'donation.donation_list_id as DonationListId')
                    ->where('donation_list_id', '!=', 0)
                    ->orderBy('donation.created_at','desc')
                    ->get();
    }

    public function create(){

        $data['action']                = route('donation_store');
        $data['action_name']           = 'Add';
        $data['frequency_id']          = old('frequency_id');
        $data['donation_category_id']  = old('donation_category_id');
        $data['user_id']               = old('user_id');
        $data['start_date']            = old('start_date');
        $data['end_date']              = old('end_date');
        $data['amount']                = old('amount');
        $data['status']                = old('status');

        return $data;
    }

    public function save($request, $id = 0){
        $action     = ($id == 0) ? 'donation_create' : 'donation_edit';

        $input      = $request->except(['_token','confirm']);

        $messages   = [
            'required' => 'The :attribute is required',
        ];
        $validator  = Validator::make($input, [
            'frequency_id'              => 'required',
            'donation_category_id'      => 'required',
        ], $messages);

        if($validator->fails()){
            #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        if($id == 0){
            $this->model->create($input);
            $this->model->orderBy('created_at', 'desc')->first()->assignRole(2);
            #$this->listener->setMessage('User is successfully created!');
        }else{
            $this->model->where('id',$id)->update($input);
           #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        return ['status' => true, 'results' => 'Success'];
    }

    public function edit($id){
        $data['action']         = '';
        $data['action_name']    = 'Edit';
        $donation               = $this->model->find($id);

        $data['frequency_id']          = (is_null(old('frequency_id'))?$donation->frequency_id:old('frequency_id'));
        $data['donation_category_id']  = (is_null(old('donation_category_id'))?$donation->donation_category_id:old('donation_category_id'));
        $data['donation_list_id']      = (is_null(old('donation_list_id'))?$donation->donation_list_id:old('donation_list_id'));
        $data['start_date']            = (is_null(old('start_date'))?$donation->format_startdate->format("m/d/Y"):old('start_date'));
        $data['end_date']              = (is_null(old('end_date'))?$donation->format_enddate->format("m/d/Y"):old('end_date'));
        $data['amount']                = (is_null(old('amount'))?$donation->amount:old('amount'));
        $data['status']                = (is_null(old('status'))?$donation->status:old('status'));

        return $data;
    }

    /*public function update(array $request, $id){
        $this->model->find($id)->update($request);
    }*/

     public function displayCart($cart, $slug){

        foreach($cart as $item){
            $data['frequency'] = $item->getFrequencyId();
            $data['donation_category'] = $item->getDonationCategoryId();
            $data['amount'] = $item->getAmount();
            $data['start_date'] = $item->getStartDate();
            $data['end_date'] = $item->getEndDate();
         }
         return $data;

    }
    public function cartTransaction($request){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $input      = $request->except(['confirm']);

        //insert into transaction table
        #dd($request->userid);
        $trans = new Transaction;
        $trans->user_id                 = $request->userid;
        $trans->transaction_key         = $request->_token;
        $trans->token                   = $request->_token;
        $trans->total_amount            = $request->total;
        $trans->save();

        $getTrans = $trans->orderBy('id','desc')->first();
        //insert into transaction details
        $trans = new TransactionDetails;
        $trans->transaction_id          = $getTrans->id;
        $trans->volunteer_id            = 0;
        $trans->frequency_id            = 0;
        $trans->event_id                = 0;
        $trans->save();


         return ['status' => true, 'results' => 'Success'];
    }
    // public function cartItemTransaction($request,$cart){
    //     //insert into donation table
    //     DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    //     $trans = new Transaction;
    //     $getTrans = $trans->orderBy('id','desc')->first();

    //     $data['cart'] = $cart;
    //     foreach($data['cart'] as $item){
    //         #dd($item->donation_type);
    //         $donation = new Donation;
    //         if ($item->frequency_id == '') {
    //             $item->frequency_id = 0;
    //         }
    //         if ($item->donation_category_id == '') {
    //             $item->donation_category_id = 0;
    //         }
    //         $donation->frequency_id                 = $item->frequency_id;
    //         $donation->organization_id              = $request->organization_id;
    //         $donation->transaction_id               = $getTrans->id;
    //         $donation->donation_list_id             = $item->donation_category_id;
    //         $donation->start_date                   = $item->start_date;
    //         $donation->end_date                     = $item->end_date;
    //         $donation->no_of_payments               = 0;
    //         $donation->amount                       = $item->amount;
    //         $donation->donation_type                = $item->donation_type;
    //         $donation->save();
    //     }
    //     return ['status' => true, 'results' => 'Success'];
    // }
    public function cartItemTransaction($request){
        //insert into donation table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // $trans = new Transaction;
        $getTrans = Transaction::where('token', $request->token)->first();
        $sdate= date("Y-m-d", strtotime($request->start_date));
        $edate= date("Y-m-d", strtotime($request->end_date));
            #dd($item->donation_type);
            // $start_date = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
            // $end_date   = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');
            $start_date = $request->start_date;
            $end_date   = $request->end_date;
            $donation = new Donation;
            if ($request->frequency_id == '') {
                $request->frequency_id = 0;
            }
            if ($request->donation_category_id == '') {
                $request->donation_category_id = 0;
            }
            if($request->recurring_type == 0){
                $request->no_of_payments = 0;
            }else{
                // $request->start_date = 0;
                // $request->end_date = 0;
                // $sdate= date("Y-m-d", strtotime($request->start_date));
                // $edate= date("Y-m-d", strtotime($request->end_date));
                $sdate= $request->start_date;
                $edate= $request->end_date;
            }
            if($request->no_of_payments == null){
                $request->no_of_payments =0;
            }
            // dd($request);
            $donation->frequency_id                 = $request->frequency_id;
            $donation->organization_id              = $request->organization_id;
            $donation->transaction_id               = $getTrans->id;
            $donation->donation_list_id             = $request->donation_category_id;
            $donation->start_date                   = $sdate;
            $donation->end_date                     = $edate;
            $donation->no_of_payments               = $request->no_of_payments;

            $donation->amount                       = $request->amount;
            $donation->note                         = $request->note;
            $donation->donation_type                = $request->donation_type;

            if($request->donation_type == "One-Time"){
                $donation->status = "Completed";
            }

            $donation->save();
        return ['status' => true, 'results' => 'Success'];
    }

    public function show($id){
        return $this->model->find($id);
    }


    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }

    public function cancelDonation($id){

        // dd('haynako');
        $this->model->where('id',$id)->update(['status' => 'InActive']);
    }

    public function updateDonation($request, $id)
    {
            $input      = $request->except(['_token','confirm']);
            $messages   = ['required' => 'The :attribute is required',];

            $validator  = Validator::make($input, [
                'end_date'             => 'date|after:start_date',
            ], $messages);

            $this->model->where('id',$id)->update([
                                    'amount' => $input['amount'], 
                                    'end_date' => $input['end_date'] 
                                ]);

            return ['status' => true, 'results' => 'Success'];   
    }
}
