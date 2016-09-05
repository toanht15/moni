<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignActionIdToUserCampaignMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_campaign_messages', function(Blueprint $table)
		{
			$table->integer('campaign_action_id')->unsigned()->after('campaign_message_master_id');
			$table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_campaign_messages', function(Blueprint $table)
		{
			$table->dropForeign('user_campaign_messages_campaign_action_id_foreign');
			$table->dropColumn('campaign_action_id');
		});
	}

}
