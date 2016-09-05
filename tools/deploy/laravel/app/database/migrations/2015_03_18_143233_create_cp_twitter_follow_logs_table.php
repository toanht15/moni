<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpTwitterFollowLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_twitter_follow_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('action_id')->unsigned();
            $table->string('status', 20);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('action_id')->references('id')->on('cp_twitter_follow_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cp_twitter_follow_logs');
    }
}
