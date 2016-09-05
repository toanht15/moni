<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPersonalFlgBrandUserRelation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			$table->tinyInteger('personal_info_flg')->default(1)->after('referrer');
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
			$table->dropColumn('personal_info_flg');
		});
	}

}
