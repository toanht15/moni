<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestInfoToBrandPageSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
			$table->string('test_id', 255)->after('brand_id')->nullable();
			$table->string('test_pass', 255)->after('test_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
			$table->dropColumn('test_id');
			$table->dropColumn('test_pass');
		});
	}

}
