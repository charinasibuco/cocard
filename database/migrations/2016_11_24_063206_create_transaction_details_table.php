<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transaction');
            // $table->integer('participants_id')->unsigned();
            // $table->foreign('participants_id')->references('id')->on('participants');
            $table->integer('volunteer_id')->unsigned();
            $table->foreign('volunteer_id')->references('id')->on('volunteers');
            $table->integer('frequency_id')->unsigned();
            $table->foreign('frequency_id')->references('id')->on('frequency');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('event');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
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
      Schema::drop('transaction_details');
    }
}
