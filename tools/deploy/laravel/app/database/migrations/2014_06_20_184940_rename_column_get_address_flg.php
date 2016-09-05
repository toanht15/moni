<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnGetAddressFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campaigns', function(Blueprint $table)
		{
            $table->dropColumn('get_address_flg');
            $table->tinyInteger('get_address_type')->default(0)->after('show_navigation_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('campaigns', function(Blueprint $table)
		{
            $table->dropColumn('get_address_type');
            $table->tinyInteger('get_address_flg')->default(0)->after('show_navigation_flg');
		});
	}

}
