<?php

use Illuminate\Database\Seeder;
use App\Donation;

class DonationSeeder extends Seeder
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
    			'organization_id'				=> '1',
    			'frequency_id'					=> '0',
    			'donation_list_id'				=> '1',
    			'transaction_id'				=> '1',
    			'start_date'					=> '2017-02-14 08:00:00',
    			'end_date'						=> '2017-02-14 08:00:00',
    			'no_of_payments'				=> '1',
    			'amount'						=> '500',
                'donation_type'                 => 'One-Time',
    			'status'						=> 'Completed'
    		],
    		[
    			'organization_id'				=> '1',
    			'frequency_id'					=> '2',
    			'donation_list_id'				=> '2',
    			'transaction_id'				=> '2',
    			'start_date'					=> '2017-04-17 08:00:00',
    			'end_date'						=> '2017-05-08 08:00:00',
    			'no_of_payments'				=> '3',
    			'amount'						=> '900',
                'donation_type'                 => 'Recurring',
    			'status'						=> 'Active'
    		],
    		[
    			'organization_id'				=> '2',
    			'frequency_id'					=> '0',
    			'donation_list_id'				=> '3',
    			'transaction_id'				=> '3',
    			'start_date'					=> '2017-03-09 08:00:00',
    			'end_date'						=> '2017-03-09 08:00:00',
    			'no_of_payments'				=> '1',
    			'amount'						=> '400',
                'donation_type'                 => 'One-Time',
    			'status'						=> 'Completed'
    		],
    		[
    			'organization_id'				=> '2',
    			'frequency_id'					=> '1',
    			'donation_list_id'				=> '4',
    			'transaction_id'				=> '4',
    			'start_date'					=> '2017-08-23 08:00:00',
    			'end_date'						=> '2017-08-24 08:00:00',
    			'no_of_payments'				=> '2',
    			'amount'						=> '200',
                'donation_type'                 => 'Recurring',
    			'status'						=> 'Active'
    		],
            [
                'organization_id'               => '3',
                'frequency_id'                  => '0',
                'donation_list_id'              => '5',
                'transaction_id'                => '5',
                'start_date'                    => '2017-05-27 08:00:00',
                'end_date'                      => '2017-05-27 08:00:00',
                'no_of_payments'                => '1',
                'amount'                        => '600',
                'donation_type'                 => 'One-Time',
                'status'                        => 'Completed'
            ],
            [
                'organization_id'               => '3',
                'frequency_id'                  => '3',
                'donation_list_id'              => '6',
                'transaction_id'                => '6',
                'start_date'                    => '2017-12-26 08:00:00',
                'end_date'                      => '2018-02-26 08:00:00',
                'no_of_payments'                => '2',
                'amount'                        => '300',
                'donation_type'                 => 'Recurring',
                'status'                        => 'Active'
            ]
    	];
    	foreach ($data as $key)
    	{
    		Donation::create([
                'organization_id'    		    => $key['organization_id'],
                'frequency_id'                  => $key['frequency_id'],
                'donation_list_id'			    => $key['donation_list_id'],
				'transaction_id'		        => $key['transaction_id'],
                'start_date'        	        => $key['start_date'],
                'end_date'                      => $key['end_date'],
                'no_of_payments'                => $key['no_of_payments'],
                'amount'                        => $key['amount'],
                'donation_type'                 => $key['donation_type'],
                'status'                        => $key['status']
            ]);
    	}
    }
}
