<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFanlistMigrate extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_search_info');
        Schema::create('user_search_info', function(Blueprint $table)
        {
            $table->increments('id');
            $table->BigInteger('user_id')->unsigned();
            $table->string('sex');
            $table->date('birthday');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->index('birthday');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_search_info');
    }

}
