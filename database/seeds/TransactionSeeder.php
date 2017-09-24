<?php

use Illuminate\Database\Seeder;
use App\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $data = [
    		[
    			'user_id'				=> '5',
    			'transaction_key'		=> 'QWERTYUIOP',
    			'token'					=> 'POIUYTREWQ',
    			'total_amount'			=> '500',
    			'status'				=> 'Active'
    		],
    		[
    			'user_id'				=> '5',
    			'transaction_key'		=> 'WERTYUIOP',
    			'token'					=> 'POIUYTREW',
    			'total_amount'			=> '900',
    			'status'				=> 'Active'
    		],
    		[
    			'user_id'				=> '6',
    			'transaction_key'		=> 'ASDFGHJKL',
    			'token'					=> 'LKJHGFDSA',
    			'total_amount'			=> '400',
    			'status'				=> 'Active'
    		],
    		[
    			'user_id'				=> '6',
    			'transaction_key'		=> 'SDFGHJKL',
    			'token'					=> 'KJHGFDSA',
    			'total_amount'			=> '200',
    			'status'				=> 'Active'
    		],
    		[
    			'user_id'				=> '7',
    			'transaction_key'		=> 'ZXCVBNM',
    			'token'					=> 'MNBVCXZ',
    			'total_amount'			=> '600',
    			'status'				=> 'Active'
    		],
    		[
    			'user_id'				=> '7',
    			'transaction_key'		=> 'XCVBNM',
    			'token'					=> 'NBVCXZ',
    			'total_amount'			=> '300',
    			'status'				=> 'Active'
    		]
    	];
    	foreach ($data as $key)
    	{
    		Transaction::create([
                'user_id'    		=> $key['user_id'],
                'transaction_key'   => $key['transaction_key'],
                'token'				=> $key['token'],
				'total_amount'		=> $key['total_amount'],
                'status'        	=> $key['status']
            ]);
    	}
    }
}
