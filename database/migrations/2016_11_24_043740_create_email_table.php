<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('email_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->string('name');
            $table->string('details');
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });

        Schema::create('email_group_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('email');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('birthdate');
            $table->enum('marital_status', ['Married', 'Single', 'Divorced', 'Widowed/Widower', 'Committed', 'Not Specified'])->default('Single');
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->timestamps();

            $table->foreign('email_group_id')->references('id')->on('email_groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('emails');
        Schema::drop('email_group_members');
        Schema::drop('email_groups');
    }
}
