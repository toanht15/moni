<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMpUidToRedirectors extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('redirectors', function(Blueprint $table)
		{
            $table->tinyInteger('mp_uid')->after('description');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('redirectors', function(Blueprint $table)
		{
            $table->dropColumn('mp_uid');
		});
	}

}
