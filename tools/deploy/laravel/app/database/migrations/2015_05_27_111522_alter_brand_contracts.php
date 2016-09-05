<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBrandContracts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_contracts', function(Blueprint $table)
		{
			//
            $table->dateTime('display_end_date')->default('9999-12-31 23:59:59')->after('contract_end_date');
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
			//
            $table->dropColumn('display_end_date');
		});
	}

}
