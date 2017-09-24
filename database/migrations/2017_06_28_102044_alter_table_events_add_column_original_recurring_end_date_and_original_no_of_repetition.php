<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableEventsAddColumnOriginalRecurringEndDateAndOriginalNoOfRepetition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('event',function(Blueprint $table){
            $table->integer('original_no_of_repetition');
            $table->dateTime('original_recurring_end_date');
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
