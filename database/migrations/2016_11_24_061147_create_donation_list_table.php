<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('donation_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('donation_category_id')->unsigned();
            $table->foreign('donation_category_id')->references('id')->on('donation_category');
            $table->string('name');
            $table->string('description');
            $table->integer('recurring');
            $table->enum('status', ['Active', 'InActive'])->default('Active');
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
      Schema::drop('donation_list');
    }
}
