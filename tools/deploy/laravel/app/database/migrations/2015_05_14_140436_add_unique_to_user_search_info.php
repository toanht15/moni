<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueToUserSearchInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_search_info', function(Blueprint $t)
		{
			$t->unique('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_search_info', function(Blueprint $t)
		{
			$t->dropUnique('user_search_info_user_id_unique');
		});
	}

}
