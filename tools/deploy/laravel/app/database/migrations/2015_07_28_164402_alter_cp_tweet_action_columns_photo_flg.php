<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpTweetActionColumnsPhotoFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_tweet_actions', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `cp_tweet_actions` ALTER `photo_flg` SET DEFAULT 0');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_tweet_actions', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `cp_tweet_actions` ALTER `photo_flg` SET DEFAULT 1');
		});
	}

}
