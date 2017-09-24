<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zipcode');
            $table->string('phone');
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female']);
            $table->enum('marital_status', ['Married', 'Single', 'Divorced', 'Widowed/Widower', 'Committed', 'Not Specified'])->default('Single');
            $table->string('email');
            $table->string('password', 60);
            $table->string('image')->nullable();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->enum('locale', ['en', 'es'])->default('en');
            $table->string('api_token', 60)->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
