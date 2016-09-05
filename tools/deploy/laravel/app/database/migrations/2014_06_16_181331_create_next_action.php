<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNextAction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaign_next_actions', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('campaign_action_id')->unsigned();
			$t->integer('campaign_next_action_id')->unsigned();
			$t->timestamps();
			$t->foreign('campaign_action_id')->references('id')->on('campaign_actions');
			$t->foreign('campaign_next_action_id')->references('id')->on('campaign_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaign_next_actions');
	}

}
