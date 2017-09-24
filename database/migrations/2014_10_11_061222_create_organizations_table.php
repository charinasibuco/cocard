<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_organization_user', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('contact_person');
            $table->string('position');
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('url');
            $table->string('scheme');
            $table->string('logo');
            $table->string('banner_image');
            $table->enum('status', ['Active', 'InActive', 'Declined', 'Pending'])->default('Pending');
            $table->timestamps();
        });
        Schema::create('organizations', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('contact_person');
            $table->string('position');
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('url');
            $table->string('language');
            $table->string('scheme');
            $table->string('logo');
            $table->string('banner_image');
            $table->integer('pending_organization_user_id')->unsigned();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->timestamps();

            $table->foreign('pending_organization_user_id')->references('id')->on('pending_organization_user')->onDelete('cascade');
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
