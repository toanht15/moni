<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTwitterFollows extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('twitter_follows', function(Blueprint $table) {
			$table->increments('id');
			$table->bigInteger('stream_id')->unsigned();
			$table->string('follower_id',255);
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

			$table->unique(array('stream_id','follower_id'), 'twitter_follows_unique_key');
			$table->index('stream_id', 'follower_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('twitter_follows');
	}

}
