<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserCampaignThreads extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_campaign_threads', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('campaign_id')->unsigned();
			$table->string('title');
			$table->tinyInteger('total_message_count')->default(0);
			$table->tinyInteger('unread_message_count')->default(0);
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('campaign_id')->references('id')->on('campaigns');
			$table->unique(array('campaign_id', 'user_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_campaign_threads');
	}

}
