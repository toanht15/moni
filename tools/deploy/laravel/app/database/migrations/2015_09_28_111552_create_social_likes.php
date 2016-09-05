<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialLikes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_likes', function(Blueprint $table)
		{
			$table->bigInteger('id')->unsigned();
			$table->bigInteger('user_id');
			$table->bigInteger('social_media_id');
			$table->string('like_id', 255)->default('');
			$table->bigInteger('created')->unsigned()->nullable();
			$table->index('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_likes');
	}

}
