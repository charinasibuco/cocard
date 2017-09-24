<?php

use Illuminate\Database\Seeder;
use App\DonationList;

class DonationListSeeder extends Seeder
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
    			'donation_category_id' 		=> '1',
    			'name'						=> 'Youth Encounter',
    			'description'				=> 'Millenials journeying towards salvation',
    			'recurring'					=> '0',
    			'status'					=> 'Active'
    		],
    		[
    			'donation_category_id' 		=> '1',
    			'name'						=> 'Immaculate Conception Orphanage',
    			'description'				=> 'Sustaining the needs of the children',
    			'recurring'					=> '1',
    			'status'					=> 'Active'
    		],
    		[
    			'donation_category_id' 		=> '2',
    			'name'						=> 'Celebrating Grandparents Day',
    			'description'				=> 'celebration for all lolos and lolas',
    			'recurring'					=> '0',
    			'status'					=> 'Active',
    		],
    		[
    			'donation_category_id' 		=> '2',
    			'name'						=> 'St. Magdalene of Nagasakis Home for the Aged',
    			'description'				=> 'Sustaining the needs of the oldies',
    			'recurring'					=> '1',
    			'status'					=> 'Active'
    		],
    		[
    			'donation_category_id' 		=> '3',
    			'name'						=> 'Hackathon',
    			'description'				=> 'Programmers on the go: Programming 24/7',
    			'recurring'					=> '0',
    			'status'					=> 'Active'
    		],
    		[
    			'donation_category_id' 		=> '3',
    			'name'						=> 'Programmer Funds',
    			'description'				=> 'Sustaining the needs of the programmers',
    			'recurring'					=> '1',
    			'status'					=> 'Active'
    		]
    	];
    	foreach ($data as $key)
    	{
    		DonationList::create([
    			'donation_category_id'		=> $key['donation_category_id'],
    			'name'						=> $key['name'],
    			'description'				=> $key['description'],
    			'recurring'					=> $key['recurring'],
    			'status'					=> $key['status']
    		]);
    	}
    }
}
