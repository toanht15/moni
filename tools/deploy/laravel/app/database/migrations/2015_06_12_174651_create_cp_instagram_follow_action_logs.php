<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpInstagramFollowActionLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_instagram_follow_action_logs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cooperate_status')->unsigned();
			$table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_instagram_follow_action_logs');
	}

}
