<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("staffs",function(Blueprint $table){
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string("email");
            $table->string("contact_number");
            $table->string('role');
            $table->enum("status",["Active","Inactive"]);
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
        //
    }
}
