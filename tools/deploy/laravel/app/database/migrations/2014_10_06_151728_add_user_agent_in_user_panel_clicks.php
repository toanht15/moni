<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAgentInUserPanelClicks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('user_panel_clicks', function(Blueprint $t)
        {
            $t->string('user_agent')->default('')->after('entries_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('user_panel_clicks', function(Blueprint $t)
        {
            $t->dropColumn('user_agent');
        });
	}

}
