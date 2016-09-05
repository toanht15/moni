<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpInstagramFollowUserLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_instagram_follow_user_logs', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->integer('cp_user_id')->unsigned();
            $table->string('social_media_account_id', 255);
            $table->integer('follow_status')->unsigned();
            $table->boolean('check_flg')->default(0);
            $table->boolean('del_flg')->default(0);
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
		Schema::drop('cp_instagram_follow_user_logs');
	}

}
