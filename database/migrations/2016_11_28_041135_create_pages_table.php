<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages',function(Blueprint $table){
            $table->increments('id');
            $table->integer('parent_id');
            $table->string('title');
            $table->string('title_translation')->nullable();
            $table->string('slug');
            $table->longtext('content');
            $table->string('content_translation')->nullable();
            $table->enum('status',['published','hidden']);
            $table->string('template');
            $table->string('order');
            $table->text('meta_title')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
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
        Schema::drop('pages');
    }
}
