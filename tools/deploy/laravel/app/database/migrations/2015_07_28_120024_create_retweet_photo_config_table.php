<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetweetPhotoConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('retweet_photo_configs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_retweet_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_retweet_action_id');
            $table->foreign('cp_retweet_action_id')->references('id')->on('cp_retweet_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('retweet_photo_configs');
	}

}
