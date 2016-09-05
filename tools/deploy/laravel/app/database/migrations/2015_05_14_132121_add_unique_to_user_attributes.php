<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueToUserAttributes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_attributes', function(Blueprint $t)
		{
			$t->unique(array('user_id', 'user_attribute_master_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_attributes', function(Blueprint $t)
		{
			$t->dropUnique('user_attributes_user_id_user_attribute_master_id_unique');
		});
	}

}
