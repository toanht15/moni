<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDelFlgForCampaignAction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campaigns', function(Blueprint $table)
		{
			$table->tinyInteger('del_flg')->default(0)->after('public_flg');
		});

		Schema::table('campaign_action_groups', function(Blueprint $table)
		{
			$table->tinyInteger('del_flg')->default(0)->after('order');
		});

		Schema::table('campaign_actions', function(Blueprint $table)
		{
			$table->tinyInteger('del_flg')->default(0)->after('type');
		});

		Schema::table('campaign_next_actions', function(Blueprint $table)
		{
			$table->tinyInteger('del_flg')->default(0)->after('campaign_next_action_id');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campaigns', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});

		Schema::table('campaign_action_groups', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});

		Schema::table('campaign_actions', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});

		Schema::table('campaign_next_actions', function(Blueprint $table) {
			$table->dropColumn('del_flg');
		});
	}

}
