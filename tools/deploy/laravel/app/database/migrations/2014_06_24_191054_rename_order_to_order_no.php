<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameOrderToOrderNo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campaign_action_groups', function(Blueprint $table)
		{

            $table->dropColumn("order");
            $table->tinyInteger('order_no')->default(0)->after('campaign_id');
		});

        Schema::table('campaign_actions', function(Blueprint $table)
        {

            $table->dropColumn("order");
            $table->tinyInteger('order_no')->default(0)->after('campaign_action_group_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campaign_action_groups', function(Blueprint $table)
		{
            $table->dropColumn("order_no");
            $table->tinyInteger('order')->default(0)->after('campaign_id');
        });

        Schema::table('campaign_actions', function(Blueprint $table)
        {

            $table->dropColumn("order_no");
            $table->tinyInteger('order')->default(0)->after('action_group_id');
        });
	}

}
