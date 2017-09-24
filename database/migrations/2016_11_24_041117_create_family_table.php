<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFamilyTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('family', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->string('name');
            $table->string('description');
            $table->string('primary_phone');
            $table->string('secondary_phone');
            $table->string('primary_email');
            $table->string('secondary_email');
            $table->string('address_1');
            $table->string('address_2');
            $table->string('city');
            $table->string('state');
            $table->string('zipcode');
            $table->timestamps();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
        });
        Schema::create('family_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('family_id')->unsigned();
            $table->foreign('family_id')->references('id')->on('family');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('allergies');
            $table->string('img')->nullable();
            $table->string('relationship');
            $table->string('additional_info');
            $table->integer('child_number');
            $table->timestamps();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        //
    }
}
