<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVolunteerRemoveUniqueEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // if(Schema::hasColumn('volunteers', 'email'))
        // {
        //     Schema::table('volunteers', function (Blueprint $table) {
        //         $table->dropColumn('email');
        //     });
        // }
        Schema::table('volunteers', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        Schema::table('volunteers', function(Blueprint $table){
             $table->string('email')->nullable()->default(NULL);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('volunteers', function (Blueprint $table) {
         $table->dropColumn('email');
        });
    }
}
