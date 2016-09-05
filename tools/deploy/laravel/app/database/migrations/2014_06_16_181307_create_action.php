<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaign_actions', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('action_group_id')->unsigned();
			$t->tinyInteger('order')->default(0);
			$t->tinyInteger('type')->default(0);
			$t->timestamps();
			$t->foreign('action_group_id')->references('id')->on('campaign_action_groups');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaign_actions');
	}

}
