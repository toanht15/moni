<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campaign_action_groups', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('campaign_id')->unsigned();
			$t->tinyInteger('order')->default(0);
			$t->timestamps();
			$t->foreign('campaign_id')->references('id')->on('campaigns');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('campaign_action_groups');
	}



}
