<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetweetMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('retweet_messages', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_retweet_action_id')->unsigned();
            $table->tinyInteger('skipped')->default(0);
            $table->tinyInteger('retweeted')->default(0);
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_user_id');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
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
		Schema::drop('retweet_messages');
	}

}
