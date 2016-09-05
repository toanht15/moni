<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpLineAddFriendActionLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_line_add_friend_action_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_line_add_friend_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_line_add_friend_action_id','action_id')->references('id')->on('cp_line_add_friend_actions','cp_line_action');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cp_line_add_friend_action_logs');
	}
}
