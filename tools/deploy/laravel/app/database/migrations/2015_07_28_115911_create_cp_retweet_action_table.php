<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpRetweetActionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_retweet_actions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('title', 255)->default('');
            $table->string('image_url', 512)->default('');
            $table->longText('text')->default('');
            $table->longText('html_content')->default('');
            $table->string('button_label_text', 255)->default('');
            $table->string('tweet_url', 512)->default('');
            $table->string('twitter_name', 255)->default('');
            $table->string('twitter_screen_name', 255)->default('');
            $table->string('twitter_profile_image_url', 512)->default('');
            $table->string('tweet_id', 255)->default('');
            $table->longText('tweet_text')->default('');
            $table->tinyInteger('tweet_has_photo')->default(0);
            $table->dateTime('tweet_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('skip_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_action_id');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_retweet_actions');
	}

}
