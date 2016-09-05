<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCpUserActionStatuses extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_user_action_statuses', function(Blueprint $table) {
			$table->tinyInteger("device_type")->default(0)->after("status");
			$table->string("user_agent", 256)->default("")->after("status");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_user_action_statuses', function(Blueprint $table) {
			$table->dropColumn("device_type");
			$table->dropColumn("user_agent");
		});
	}

}
