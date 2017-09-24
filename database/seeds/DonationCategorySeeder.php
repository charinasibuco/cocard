<?php

use Illuminate\Database\Seeder;

class DonationCategorySeeder extends Seeder
{
    public function run()
    { 
        $data = [
    		[
                'organization_id' => '1',
    			'name'			=> 'Youth',
    			'description'	=> 'Youth Community Program',
    			
    		],
    		[
                'organization_id' => '2',
    			'name'			=> 'Seniors',
    			'description'	=> 'Seniors Community Program',

    		],
    		[
                'organization_id' => '3',
    			'name'			=> 'Community Outreach',
    			'description'	=> 'Community Program',

    		],
    		[
                'organization_id' => '1',
    			'name'			=> 'Teen Outreach',
    			'description'	=> 'Teenage youth program',

    		]

    	];
    	foreach ($data as $key)
    	{
    		DB::table('donation_category')->insert([
                'organization_id'   => $key['organization_id'],
    			'name'				=> $key['name'],
    			'description'		=> $key['description'],
    		]);
    	}
    }
}
