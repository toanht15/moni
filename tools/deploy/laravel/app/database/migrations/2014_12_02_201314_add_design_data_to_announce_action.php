<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignDataToAnnounceAction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_announce_actions', function(Blueprint $table)
		{
			$table->tinyInteger('design_type')->after('text')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_announce_actions', function(Blueprint $table)
		{
			$table->dropColumn('design_type');
		});
	}

}
