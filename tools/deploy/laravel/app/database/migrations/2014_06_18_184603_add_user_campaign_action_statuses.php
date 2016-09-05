<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserCampaignActionStatuses extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_campaign_action_statuses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('campaign_action_id')->unsigned();
			$table->tinyInteger('status')->default(0);
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
			$table->unique(array('user_id', 'campaign_action_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_campaign_action_statuses');
	}

}
