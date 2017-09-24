<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVolunteerGroupAddNoOfOccurrenceAndTotalNoOfOccurrence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('volunteer_groups',function(Blueprint $table){
            $table->integer('no_of_occurrence');
            $table->integer('total_no_of_occurrence');
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
