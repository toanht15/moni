<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusForCampaignActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campaign_actions', function(Blueprint $table)
		{
            $table->tinyInteger('status')->default(0)->after('type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campaign_actions', function(Blueprint $table)
		{
			$table->dropColumn('status');
		});
	}

}
