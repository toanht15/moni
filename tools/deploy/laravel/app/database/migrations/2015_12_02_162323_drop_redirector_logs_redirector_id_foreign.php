<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRedirectorLogsRedirectorIdForeign extends Migration {

	public function up()
	{
		Schema::table('redirector_logs', function(Blueprint $table)
		{
			$table->dropForeign("redirector_logs_redirector_id_foreign");
		});
	}

	public function down()
	{
		Schema::table('redirector_logs', function(Blueprint $table)
		{
			$table->foreign("redirector_id")->references("id")->on("redirectors");
		});
	}

}
