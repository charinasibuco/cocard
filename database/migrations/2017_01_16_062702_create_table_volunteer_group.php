<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableVolunteerGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        if(Schema::hasTable("volunteer_role")){
            Schema::drop("volunteer_role");
        }

        if(Schema::hasTable("volunteers")){
            Schema::drop("volunteers");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::create('volunteers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('volunteer_group_id');
            $table->enum('volunteer_group_status',["Pending","Approved","Rejected"])->default('Pending');
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->timestamps();
        });

        Schema::create("volunteer_groups",function(Blueprint $table){
            $table->increments("id");
            $table->string("type");
            $table->integer("volunteers_needed");
            $table->text("note");
            $table->integer("event_id");
            $table->enum("status",["Active","InActive"])->default('Active');
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

    }
}
