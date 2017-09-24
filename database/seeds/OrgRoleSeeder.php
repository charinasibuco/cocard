<?php

use Illuminate\Database\Seeder;

class OrgRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert( [
            'title' => 'staff',
            'description' => 'back office user of an organization.'
        ]);
    }
}
