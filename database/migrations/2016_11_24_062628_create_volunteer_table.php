<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('event');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->string('title');
            $table->string('description');
            $table->timestamps();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
        });

        Schema::create('volunteers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('volunteer_role_id')->unsigned();
            $table->foreign('volunteer_role_id')->references('id')->on('volunteer_role');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('event');
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
