<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOptinFlgToBrandsUsersRelations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			//
            $table->tinyInteger('optin_flg')->default(1)->after('admin_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			$table->dropColumn('optin_flg');
		});
	}

}
