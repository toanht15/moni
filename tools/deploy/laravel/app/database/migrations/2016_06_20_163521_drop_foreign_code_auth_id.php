<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignCodeAuthId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_code_authentication_actions', function(Blueprint $table)
		{
            $table->dropForeign('cp_code_authentication_actions_code_auth_id_foreign');
            $table->dropIndex("cp_code_authentication_actions_code_auth_id_index");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_code_authentication_actions', function(Blueprint $table)
		{

		});
	}

}
