<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueToUserIdOfShippingAddresses extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('shipping_addresses', function(Blueprint $t)
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
		Schema::table('shipping_addresses', function(Blueprint $t)
		{
			$t->dropUnique('shipping_addresses_user_id_unique');
		});
	}

}
