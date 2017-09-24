<?php
namespace Acme\Repositories;
use App\Http\Requests\Request;
use Acme\Repositories\Repository;
use Illuminate\Support\Facades\Validator;
use App\Transaction;
use App\TransactionDetails;
use DB;
class TransactionRepository extends Repository{

	public function model(){
		return 'App\Transaction';
	}

	public function getUserTransaction($id)
    {
        $query = $this->model->where('user_id', '=', $id);
        return $query->select('transaction.*');
    }

	public function save($request){
        //dd($request);
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
		$transaction = $this->model;
        $transaction->user_id           = $request->user_id;
        $transaction->transaction_key   = $request->transaction_key;
        $transaction->token             = $request->token;
        $transaction->total_amount      = $request->total_amount;
        $transaction->save();

        $transaction_details                    = new TransactionDetails;
        $transaction_details->transaction_id    = $transaction->id;
        $transaction_details->volunteer_id      = 0;
        $transaction_details->frequency_id      = 0;
        $transaction_details->event_id          = $request->event_id;
        $transaction_details->save();
	}


	 public function create(){
	 	//
    }
    public function edit($id){
    	//
    }

    public function destroy($id){

       //
    }
}