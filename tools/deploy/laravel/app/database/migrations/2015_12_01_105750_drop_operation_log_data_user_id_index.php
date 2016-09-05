<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOperationLogDataUserIdIndex extends Migration {

	public function up()
	{
		Schema::table('operation_log_admin_data', function(Blueprint $table)
		{
			$table->dropForeign("operation_log_data_user_id_foreign");
			$table->dropIndex("operation_log_data_user_id_index");
		});
	}

	public function down()
	{
		Schema::table('operation_log_admin_data', function(Blueprint $table)
		{
			$table->index("user_id", "operation_log_data_user_id_index");
			$table->foreign("user_id", "operation_log_data_user_id_foreign")->references("id")->on("users");
		});
	}

}
