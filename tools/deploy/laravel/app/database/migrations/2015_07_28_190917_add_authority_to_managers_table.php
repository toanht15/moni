<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthorityToManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('managers', function(Blueprint $table)
		{
			$table->tinyInteger('authority')->default(0)->after('login_lockout_reset_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('managers', function(Blueprint $table)
		{
			$table->dropColumn('authority');
		});
	}

}
