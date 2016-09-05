<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPanelTypeToUserPanelClicks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('user_panel_clicks', function(Blueprint $table) {
            $table->tinyInteger("panel_type")->default(1)->after("user_id");
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('user_panel_clicks', function(Blueprint $table) {
            $table->dropColumn("panel_type");
        });
	}

}
