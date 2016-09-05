<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrderItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_items', function(Blueprint $table)
		{
			$table->string('delivery_id')->after('product_item_id');
			$table->index('delivery_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order_items', function(Blueprint $table)
		{
			$table->dropColumn('delivery_id');
		});
	}

}
