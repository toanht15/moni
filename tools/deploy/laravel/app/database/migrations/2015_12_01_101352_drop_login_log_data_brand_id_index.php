<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLoginLogDataBrandIdIndex extends Migration {

	public function up()
	{
		Schema::table('login_log_data', function(Blueprint $table)
		{
			$table->dropForeign("login_log_data_brand_id_foreign");
			$table->dropIndex("login_log_data_brand_id_index");
		});
	}

	public function down()
	{
		Schema::table('login_log_data', function(Blueprint $table)
		{
			$table->index("brand_id", "login_log_data_brand_id_index");
			$table->foreign("brand_id")->references("id")->on("brands");
		});
	}
}
