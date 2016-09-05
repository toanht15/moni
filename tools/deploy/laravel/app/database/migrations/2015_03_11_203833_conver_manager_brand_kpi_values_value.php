<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConverManagerBrandKpiValuesValue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manager_brand_kpi_values', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `manager_brand_kpi_values` MODIFY `value` int(10)');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manager_brand_kpi_values', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `manager_brand_kpi_values` MODIFY `value` varchar(255)');
		});
	}

}
