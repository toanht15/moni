<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignMessageMasters extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaign_message_masters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('campaign_id')->unsigned();
			$table->integer('campaign_action_id')->unsigned();
			$table->longText('contents');
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
			$table->foreign('campaign_id')->references('id')->on('campaigns');
			$table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
			$table->unique(array('campaign_id', 'campaign_action_id'), 'u_campaign_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaign_message_masters');
	}

}
