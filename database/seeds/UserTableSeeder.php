<?php

use Illuminate\Database\Seeder;
use App\User;
class UserTableSeeder extends Seeder
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
                'organization_id'   => '0',
                'first_name'        => 'Super',
                'last_name'         => 'Admin',
                'middle_name'       => 'User',
                'address'           => 'Address',
                'city'          	=> 'City',
                'state'            	=> 'State',
                'zipcode'           => 'Zipcode',
                'phone'        		=> '(111) 111-1111',
                'birthdate'        	=> '00-00-0000',
                'gender'        	=> 'Male',
                'email'         	=> 'superadmin@gmail.com',
                'password'          => 'superadmin',
                'status'            => 'Active'
            ],
            [
                'organization_id'   => '1',
                'first_name'        => 'Anna',
                'last_name'         => 'Grey',
                'middle_name'       => 'Steele',
                'address'           => 'San Juan St.',
                'city'              => 'Bacolod',
                'state'             => 'Negros Occidental',
                'zipcode'           => '6100',
                'phone'             => '(098) 765-4321',
                'birthdate'         => '1995-08-20',
                'gender'            => 'Female',
                'email'             => 'qpp-admin@gmail.com',
                'password'          => 'admin',
                'status'            => 'Active'
            ],
            [
                'organization_id'   => '2',
                'first_name'        => 'Liza',
                'last_name'         => 'Soberano',
                'middle_name'       => 'Hanley',
                'address'           => 'Monroe St.',
                'city'              => 'Santa Clara',
                'state'             => 'California',
                'zipcode'           => '95050',
                'phone'             => '(123) 456-7890',
                'birthdate'         => '1998-01-04',
                'gender'            => 'Female',
                'email'             => 'ca-admin@gmail.com',
                'password'          => 'admin',
                'status'            => 'Active'
            ],
            [
                'organization_id'   => '3',
                'first_name'        => 'Selena Marie',
                'last_name'         => 'Gomez',
                'middle_name'       => 'Cornett',
                'address'           => 'W Oakdale Rd',
                'city'              => 'Grand Prairie',
                'state'             => 'Texas',
                'zipcode'           => '75050',
                'phone'             => '(789) 123-4560',
                'birthdate'         => '1992-07-22',
                'gender'            => 'Female',
                'email'             => 'tt-admin@gmail.com',
                'password'          => 'admin',
                'status'            => 'Active'
            ],
            [
                'organization_id'   => '1',
                'first_name'        => 'Alyson',
                'last_name'         => 'Stoner',
                'middle_name'       => 'Hodges',
                'address'           => 'San Juan St.',
                'city'              => 'Bacolod',
                'state'             => 'Negros Occidental',
                'zipcode'           => '6100',
                'phone'             => '(456) 789-1230',
                'birthdate'         => '1993-08-11',
                'gender'            => 'Female',
                'email'             => 'astoner@gmail.com',
                'password'          => 'user',
                'status'            => 'Active'
            ],
            [
                'organization_id'   => '2',
                'first_name'        => 'Dylan Thomas',
                'last_name'         => 'Sprouse',
                'middle_name'       => 'Wright',
                'address'           => 'Monroe St.',
                'city'              => 'Santa Clara',
                'state'             => 'California',
                'zipcode'           => '95050',
                'phone'             => '(098) 123-4567',
                'birthdate'         => '1992-08-04',
                'gender'            => 'Male',
                'email'             => 'dsprouse@gmail.com',
                'password'          => 'user',
                'status'            => 'Active'
            ],
            [
                'organization_id'   => '3',
                'first_name'        => 'Theodore Peter James',
                'last_name'         => 'Taptiklis',
                'middle_name'       => 'Kinnaird',
                'address'           => 'W Oakdale Rd',
                'city'              => 'Grand Prairie',
                'state'             => 'Texas',
                'zipcode'           => '75050',
                'phone'             => '(123) 098-4567',
                'birthdate'         => '1984-12-16',
                'gender'            => 'Male',
                'email'             => 'ttaptiklis@gmail.com',
                'password'          => 'user',
                'status'            => 'Active'
            ]
        ];
        foreach ($data as $key)
        {
            User::create([
                'organization_id'   => $key['organization_id'],
                'first_name'        => $key['first_name'],
                'last_name'         => $key['last_name'],
                'middle_name'	    => $key['middle_name'],
				'address'		    => $key['address'],
				'city'			    => $key['city'],
				'state'			    => $key['state'],
				'zipcode'		    => $key['zipcode'],
				'phone'			    => $key['phone'],
                'birthdate'         => $key['birthdate'],
                'gender'            => $key['gender'],
                'email'             => $key['email'],
                'password'          => bcrypt($key['password']),
                'status'            => $key['status'],
                'api_token'         => str_random(60)
            ]);
        }
    }
}
