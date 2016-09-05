<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetPhotoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tweet_photos', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('tweet_message_id')->unsigned();
            $table->string('image_url', 255)->default('');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('tweet_message_id');
            $table->foreign('tweet_message_id')->references('id')->on('tweet_messages');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tweet_photos');
	}

}
