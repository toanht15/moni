<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterManagerKpiColumnsValue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manager_kpi_values', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `manager_kpi_values` MODIFY `value` DECIMAL(10, 2)');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manager_kpi_values', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `manager_kpi_values` MODIFY `value` INT(10)');
		});
	}

}
