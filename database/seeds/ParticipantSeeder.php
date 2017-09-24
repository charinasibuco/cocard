<?php

use Illuminate\Database\Seeder;
use App\Participant;

class ParticipantSeeder extends Seeder
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
    			'user_id'			=> '4',
    			'event_id'			=> '1',
                'name'              => 'Selena Marie Gomez',
                'email'             => 'tt-admin@gmail.com',
    			'qty'				=> '5',
    			'status'			=> 'Active'	
    		],
    		[
    			'user_id'			=> '4',
    			'event_id'			=> '2',
                'name'              => 'Selena Marie Gomez',
                'email'             => 'tt-admin@gmail.com',
    			'qty'				=> '7',
    			'status'			=> 'Active'	
    		],
    		[
    			'user_id'			=> '5',
    			'event_id'			=> '3',
                'name'              => 'Alyson Stoner',
                'email'             => 'astoner@gmail.com',
    			'qty'				=> '9',
    			'status'			=> 'Active'
    		],
    		[
    			'user_id'			=> '5',
    			'event_id'			=> '4',
                'name'              => 'Alyson Stoner',
                'email'             => 'astoner@gmail.com',
    			'qty'				=> '5',
    			'status'			=> 'Active'
    		],
    		[
    			'user_id'			=> '6',
    			'event_id'			=> '5',
                'name'              => 'Dylan Thomas Sprouse',
                'email'             => 'dsprouse@gmail.com',
    			'qty'				=> '8',
    			'status'			=> 'Active'
    		],
    		[
    			'user_id'			=> '6',
    			'event_id'			=> '6',
                'name'              => 'Dylan Thomas Sprouse',
                'email'             => 'dsprouse@gmail.com',
    			'qty'				=> '4',
    			'status'			=> 'Active'
    		]

    	];
    	foreach ($data as $key)
    	{
    		Participant::create([
    			'user_id'			=> $key['user_id'],
    			'event_id'			=> $key['event_id'],
                'name'              => $key['name'],
                'email'             => $key['email'],
    			'qty'				=> $key['qty'],
    			'status'			=> $key['status']
    		]);
    	}
    }
}
