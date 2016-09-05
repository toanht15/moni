<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tweet_messages', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_tweet_action_id')->unsigned();
            $table->tinyInteger('has_photo')->default(0);
            $table->tinyInteger('skipped')->default(0);
            $table->longText('tweet_text')->default('');
            $table->string('tweet_content_url', 255)->default('');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_user_id');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_tweet_action_id')->references('id')->on('cp_tweet_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tweet_messages');
	}

}
