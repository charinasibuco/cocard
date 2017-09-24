<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->string('name');
            $table->string('description');
            $table->integer('capacity');
            $table->integer('pending');
            $table->float('fee');
            $table->integer('parent_event_id');
            $table->string('modify_recurring_month');
            $table->integer('recurring');
            $table->integer('no_of_repetition');
            $table->dateTime('recurring_end_date');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('reminder_date');
            $table->integer('volunteer_number');
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->timestamps();
        });
         Schema::create('participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('event_id')->unsigned();
            $table->string('name');
            $table->string('email');
            $table->foreign('event_id')->references('id')->on('event');
            $table->integer('qty');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('no_of_repetition');
            $table->integer('occurence');
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
