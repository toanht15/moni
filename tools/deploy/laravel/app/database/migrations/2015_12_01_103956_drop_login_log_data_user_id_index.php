<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLoginLogDataUserIdIndex extends Migration {

	public function up()
	{
		Schema::table('login_log_data', function(Blueprint $table)
		{
			$table->dropForeign("login_log_data_user_id_foreign");
			$table->dropIndex("login_log_data_user_id_index");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('login_log_data', function(Blueprint $table)
		{
			$table->index("user_id", "login_log_data_user_id_index");
			$table->foreign("user_id")->references("id")->on("users");
		});
	}

}
