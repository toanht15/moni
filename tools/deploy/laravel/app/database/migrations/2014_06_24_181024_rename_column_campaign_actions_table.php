<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnCampaignActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        Schema::table('campaign_actions', function(Blueprint $table)
        {

            $table->dropForeign('campaign_actions_action_group_id_foreign');
            $table->dropColumn('action_group_id');

            $table->integer('campaign_action_group_id')->unsigned()->after('id');
            $table->foreign('campaign_action_group_id')->references('id')->on('campaign_action_groups');

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

            $table->dropForeign('campaign_actions_campaign_action_group_id_foreign');
            $table->dropColumn('campaign_action_group_id');

            $table->integer('action_group_id')->unsigned()->after('id');
            $table->foreign('action_group_id')->references('id')->on('campaign_action_groups');

        });
	}

}
