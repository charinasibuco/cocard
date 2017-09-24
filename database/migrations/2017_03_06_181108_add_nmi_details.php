<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNmiDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations',function(Blueprint $table){
            $table->string('nmi_user')->after('pending_organization_user_id');
            $table->string('nmi_pass')->after('nmi_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function($table) {
            $table->dropColumn('nmi_user');
            $table->dropColumn('nmi_pass');
        });
    }
}
