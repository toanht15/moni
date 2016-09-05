<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScheduledCampaigns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scheduled_campaigns', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('campaign_id')->unsigned();
			$table->dateTime('public_date')->default('0000-00-00 00:00:00');
			$table->tinyInteger('status')->default(0);
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
			$table->foreign('campaign_id')->references('id')->on('campaigns');
			$table->unique('campaign_id');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('scheduled_campaigns');
	}

}
