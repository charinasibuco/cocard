<?php

use Illuminate\Database\Seeder;

class FrequencySeeder extends Seeder
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
    			'title'			=> 'Daily',
    			'description'	=> 'Daily donation',
    			
    		],
    		[
    			'title'			=> 'Weekly',
    			'description'	=> 'Weekly donation',

    		],
    		[
    			'title'			=> 'Monthly',
    			'description'	=> 'Monthly donation',

    		],
    		[
    			'title'			=> 'Quarterly',
    			'description'	=> 'Four times in a year donation',

    		],
    		[
    			'title'			=> 'Bi-annual',
    			'description'	=> 'Twice in a year donation',

    		],
    		[
    			'title'			=> 'Yearly',
    			'description'	=> 'Once in a year donation',

    		]

    	];
    	foreach ($data as $key)
    	{
    		DB::table('frequency')->insert([
    			'title'				=> $key['title'],
    			'description'		=> $key['description'],
    		]);
    	}
    }
}
