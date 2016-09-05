<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserPanelClicksDeleteRelation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_panel_clicks', function(Blueprint $table)
		{
            $table->dropForeign('user_panel_clicks_user_id_foreign');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_panel_clicks', function(Blueprint $table)
		{
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

}
