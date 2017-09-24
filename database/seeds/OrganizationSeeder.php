<?php

use Illuminate\Database\Seeder;
use App\Organization;

class OrganizationSeeder extends Seeder
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
                'name'   							=> 'Queen of Peace Parish Church',
                'contact_person'   					=> 'Anna',
                'position'   						=> 'Admin',
                'contact_number'   					=> '(098)-765-4321',
                'email'   							=> 'qpp-admin@gmail.com',
                'password'   						=> 'admin',
                'url'   							=> 'queen-of-peace-parish',
                'language'   						=> '',
                'scheme'   							=> '',
                'logo'   							=> '',
                'pending_organization_user_id'   	=> '1',
                'status'   							=> 'Active'
            ],
            [
                'name'   							=> 'Church Alpha',
                'contact_person'   					=> 'Liza',
                'position'   						=> 'Admin',
                'contact_number'   					=> '(123)-456-7890',
                'email'   							=> 'ca-admin@gmail.com',
                'password'   						=> 'admin',
                'url'   							=> 'church-alpha',
                'language'   						=> '',
                'scheme'   							=> '',
                'logo'   							=> '',
                'pending_organization_user_id'   	=> '2',
                'status'   							=> 'Active'
            ],
            [
                'name'   							=> 'TAS Tradesoft',
                'contact_person'   					=> 'Selena',
                'position'   						=> 'Admin',
                'contact_number'   					=> '(789)-123-4560',
                'email'   							=> 'tt-admin@gmail.com',
                'password'   						=> 'admin',
                'url'   							=> 'tastradesoft',
                'language'   						=> '',
                'scheme'   							=> '',
                'logo'   							=> '',
                'pending_organization_user_id'   	=> '3',
                'status'   							=> 'Active'
            ]
        ];
        foreach ($data as $key)
        {
            Organization::create([
                'name'    							=> $key['name'],
                'contact_person'     				=> $key['contact_person'],
                'position'							=> $key['position'],
				'contact_number'					=> $key['contact_number'],
				'email'								=> $key['email'],
                'password'      					=> bcrypt($key['password']),
				'url'								=> $key['url'],
				'language'							=> $key['language'],
				'scheme'							=> $key['scheme'],
				'logo'								=> $key['logo'],
				'pending_organization_user_id'		=> $key['pending_organization_user_id'],
                'status'        					=> $key['status']
            ]);        
        }
    }
}
