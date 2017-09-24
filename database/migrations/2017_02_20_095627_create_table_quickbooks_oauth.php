<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuickbooksOauth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("cocard_quickbooks_oauth",function(Blueprint $table){
            $table->increments("id");
            $table->integer("organization_id");
            $table->string("qb_company_id")->default(null)->nullable();
            $table->string("qb_consumer_key")->default(null)->nullable();
            $table->string("qb_token")->default(null)->nullable();
            $table->string("qb_consumer_secret")->default(null)->nullable();
            $table->string("oauth_request_token")->default(null)->nullable();
            $table->string("oauth_request_token_secret")->default(null)->nullable();
            $table->string("oauth_access_token")->default(null)->nullable();
            $table->string("oauth_access_token_secret")->default(null)->nullable();
            $table->string("oauth_verifier")->default(null)->nullable();
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
