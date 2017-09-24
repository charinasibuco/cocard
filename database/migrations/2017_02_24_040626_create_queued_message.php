<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueuedMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('queued_message',function(Blueprint $table){
            $table->increments('id');
            $table->integer('event_id');
            $table->integer('volunteer_group_id');
            $table->string('subject');
            $table->longtext('message');
            $table->string('email');
            $table->dateTime('event_date');
            $table->dateTime('reminder_date');
            $table->enum('status', ['queued', 'sent'])->default('queued');
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
        Schema::drop('queued_message');
    }
}
