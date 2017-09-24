<?php

use Illuminate\Database\Seeder;
use App\Event;

class EventSeeder extends Seeder
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
    			'organization_id'			=> '1',
    			'name'						=> 'Solidarity Night',
    			'description'				=> 'Concert for a cause',
    			'capacity'					=> '1250',
    			'pending'					=> '0',	
                'fee'                       => '250',
                'parent_event_id'           => '0',
    			'modify_recurring_month'	=> '',
                'recurring'                 => '1',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-31  18:30:00',
                'start_date'                => '2017-10-31  18:30:00',
                'end_date'                  => '2017-10-31  18:30:00',
                'reminder_date'             => '2017-10-31  18:30:00',
    			'volunteer_number'			=> '100',
    			'status'					=> 'Active'
    		],
    		[
    			'organization_id'			=> '1',
    			'name'						=> 'Medical Mission',
    			'description'				=> 'A good samaritan',
    			'capacity'					=> '1500',
    			'pending'					=> '0',	
    			'fee'						=> '0',
                'parent_event_id'           => '0',
                'modify_recurring_month'    => '',
                'recurring'                 => '2',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-31  18:30:00',
                'start_date'                => '2017-10-31  18:30:00',
                'end_date'                  => '2017-10-31  18:30:00',
                'reminder_date'             => '2017-10-31  18:30:00',
    			'volunteer_number'			=> '350',
    			'status'					=> 'Active'
    		],
            [
                'organization_id'           => '2',
                'name'                      => 'Event Once Only',
                'description'               => 'Gathered as one',
                'capacity'                  => '22',
                'pending'                   => '0', 
                'fee'                       => '30',
                'parent_event_id'           => '0',
                'modify_recurring_month'    => '',
                'recurring'                 => '0',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '',
                'start_date'                => '2017-10-31  18:30:00',
                'end_date'                  => '2017-10-31  18:30:00',
                'reminder_date'             => '2017-10-31  18:30:00',
                'volunteer_number'          => '100',
                'status'                    => 'Active'
            ],
    		[
    			'organization_id'			=> '2',
    			'name'						=> 'Yearly Celebration of Faith',
    			'description'				=> 'Millenials gathered as one',
    			'capacity'					=> '1450',
    			'pending'					=> '0',	
    			'fee'						=> '300',
                'parent_event_id'           => '0',
                'modify_recurring_month'    => '',
                'recurring'                 => '3',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-31  18:30:00',
                'start_date'                => '2017-10-31  18:30:00',
                'end_date'                  => '2017-10-31  18:30:00',
                'reminder_date'             => '2017-10-31  18:30:00',
    			'volunteer_number'			=> '100',
    			'status'					=> 'Active'
    		],
    		[
    			'organization_id'			=> '2',
    			'name'						=> 'Monthly Gathering',
    			'description'				=> 'Journeying towards salvation',
    			'capacity'					=> '1275',
    			'pending'					=> '0',	
    			'fee'						=> '500',
                'parent_event_id'           => '0',
                'modify_recurring_month'    => '',
                'recurring'                 => '2',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-30  18:30:00',
                'start_date'                => '2017-10-30  18:30:00',
                'end_date'                  => '2017-10-30  18:30:00',
                'reminder_date'             => '2017-10-30  18:30:00',
    			'volunteer_number'			=> '300',
    			'status'					=> 'Active'
    		],
            [
                'organization_id'           => '2',
                'name'                      => 'Weekly Mass',
                'description'               => 'Holy Mass',
                'capacity'                  => '1215',
                'pending'                   => '0', 
                'fee'                       => '0',
                'parent_event_id'           => 0,
                'modify_recurring_month'    => '',
                'recurring'                 => '1',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-29  18:30:00',
                'start_date'                => '2017-10-29  18:30:00',
                'end_date'                  => '2017-10-29  18:30:00',
                'reminder_date'             => '2017-10-29  18:30:00',
                'volunteer_number'          => '300',
                'status'                    => 'Active'
            ],
    		[
    			'organization_id'			=> '3',
    			'name'						=> 'Hackathon',
    			'description'				=> 'Programmers on the go',
    			'capacity'					=> '320',
    			'pending'					=> '0',	
    			'fee'						=> '150',
                'parent_event_id'           => 0,
                'modify_recurring_month'    => '',
                'recurring'                 => '2',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-29  18:30:00',
                'start_date'                => '2017-10-29  18:30:00',
                'end_date'                  => '2017-10-29  18:30:00',
                'reminder_date'             => '2017-10-29  18:30:00',
    			'volunteer_number'			=> '10',
    			'status'					=> 'Active'
    		],
    		[
    			'organization_id'			=> '3',
    			'name'						=> 'TAS Tradesoft Christmas Party',
    			'description'				=> 'Celebrating holiday',
    			'capacity'					=> '320',
    			'pending'					=> '0',	
    			'fee'						=> '200',
                'parent_event_id'           => 0,
                'modify_recurring_month'    => '',
                'recurring'                 => '1',
                'no_of_repetition'          => '0',
                'recurring_end_date'        => '2019-10-29  18:30:00',
                'start_date'                => '2017-10-29  18:30:00',
                'end_date'                  => '2017-10-29  18:30:00',
                'reminder_date'             => '2017-10-29  18:30:00',
    			'volunteer_number'			=> '20',
    			'status'					=> 'Active'
    		]

    	];
    	foreach ($data as $key)
    	{
    		Event::create([
                'organization_id'    	=> $key['organization_id'],
                'name'     				=> $key['name'],
                'description'			=> $key['description'],
				'capacity'				=> $key['capacity'],
				'pending'				=> $key['pending'],
                'fee'                   => $key['fee'],
                'parent_event_id'       => $key['parent_event_id'],
                'modify_recurring_month'=> $key['modify_recurring_month'],
                'recurring'             => $key['recurring'],
				'recurring_end_date'	=> $key['recurring_end_date'],
    			'start_date'			=> $key['start_date'],
				'end_date'				=> $key['end_date'],
				'reminder_date'			=> $key['reminder_date'],
				'volunteer_number'		=> $key['volunteer_number'],
                'status'        		=> $key['status']
            ]);
    	}
    }
}
