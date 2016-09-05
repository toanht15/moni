<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserCampaignMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_campaign_messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_campaign_thread_id')->unsigned();
			$table->integer('campaign_message_master_id')->unsigned();
			$table->longText('contents');
			$table->tinyInteger('read_flg')->default(0);
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
			$table->foreign('user_campaign_thread_id')->references('id')->on('user_campaign_threads');
			$table->foreign('campaign_message_master_id')->references('id')->on('campaign_message_masters');
			$table->unique(array('user_campaign_thread_id', 'campaign_message_master_id'), 'u_user_campaign_thread_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_campaign_messages');
	}

}
