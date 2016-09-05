<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLoginLogAdminDataUserIdIndex extends Migration {

	public function up()
	{
		Schema::table('login_log_admin_data', function(Blueprint $table)
		{
			$table->dropForeign("login_log_admin_data_user_id_foreign");
		});
	}

	public function down()
	{
		Schema::table('login_log_admin_data', function(Blueprint $table)
		{
			$table->foreign("user_id")->references("id")->on("users");
		});
	}

}
