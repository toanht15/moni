<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoginLogData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('login_log_data', function($t)
		{
			$t->string('ip_address',30)->default('')->after('device');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('login_log_data', function(Blueprint $t)
		{
			$t->dropColumn('ip_address');
		});
	}

}
