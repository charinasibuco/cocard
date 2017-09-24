<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(PendingOrganizationUserSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(FrequencySeeder::class);
        $this->call(DonationCategorySeeder::class);
        $this->call(DonationListSeeder::class);
        $this->call(TransactionSeeder::class);
        $this->call(DonationSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(ParticipantSeeder::class);
        // $this->call(OrgRoleSeeder::class);
    }
}
