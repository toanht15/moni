<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSettlementDelFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->boolean('del_flg')->after('updated_at');
		});
		Schema::table('order_items', function(Blueprint $table)
		{
			$table->boolean('del_flg')->after('updated_at');
		});
		Schema::table('pre_orders', function(Blueprint $table)
		{
			$table->boolean('del_flg')->after('updated_at');
		});
		Schema::table('products', function(Blueprint $table)
		{
			$table->boolean('del_flg')->after('updated_at');
		});
		Schema::table('product_items', function(Blueprint $table)
		{
			$table->boolean('del_flg')->after('updated_at');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});
		Schema::table('order_items', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});
		Schema::table('pre_orders', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});
		Schema::table('products', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});
		Schema::table('product_items', function(Blueprint $table)
		{
			$table->dropColumn('del_flg');
		});
	}

}
