<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForProductionFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_contracts', function(Blueprint $table)
		{
			$table->tinyInteger('for_production_flg')->after('operation');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_contracts', function(Blueprint $table)
		{
            $table->dropColumn('for_production_flg');
        });
	}

}
