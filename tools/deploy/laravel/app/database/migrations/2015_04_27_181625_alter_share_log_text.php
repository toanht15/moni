<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShareLogText extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_share_user_logs', function(Blueprint $table)
		{
            $table->longText('text')->after("type");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_share_user_logs', function(Blueprint $table)
		{
            $table->dropColumn('text');
		});
	}

}
