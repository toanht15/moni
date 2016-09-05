<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullcontrolFlgToManagers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('managers', function(Blueprint $table)
		{
            $table->boolean('full_control_flg')->default(0)->after('login_lockout_reset_date');
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
            $table->dropColumn('full_control_flg');
		});
	}

}
