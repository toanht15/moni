<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpYoutubeChannel extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_youtube_channel_actions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedInteger('cp_action_id');
            $table->string('title', 255);
            $table->boolean('intro_flg')->default(0);
            $table->boolean('del_flg')->default(0);
			$table->timestamps();
            $table->unique('cp_action_id');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::create('cp_youtube_channel_accounts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('cp_youtube_channel_action_id');
            $table->unsignedInteger('brand_social_account_id');
            $table->unsignedInteger('youtube_entry_id');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->unique('cp_youtube_channel_action_id');
            $table->foreign('cp_youtube_channel_action_id', 'yt_action_foreign')->references('id')->on('cp_youtube_channel_actions');
            $table->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
        });

        Schema::create('cp_youtube_channel_user_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('cp_action_id');
            $table->unsignedInteger('cp_user_id');
            $table->integer('status');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->unique(array('cp_action_id', 'cp_user_id'));
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
        Schema::drop('cp_youtube_channel_user_logs');
        Schema::drop('cp_youtube_channel_accounts');
        Schema::drop('cp_youtube_channel_actions');
	}

}
