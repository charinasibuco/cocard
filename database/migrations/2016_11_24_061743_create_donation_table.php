<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('frequency_id')->unsigned();
            $table->foreign('frequency_id')->references('id')->on('frequency');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transaction');
            $table->integer('donation_list_id')->unsigned();
            $table->foreign('donation_list_id')->references('id')->on('donation_list');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('no_of_payments');
            $table->float('amount');
            $table->string('note')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->enum('donation_type', ['One-Time', 'Recurring']);
            $table->enum('status', ['Active', 'InActive', 'Completed'])->default('Active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('donation');
    }
}
