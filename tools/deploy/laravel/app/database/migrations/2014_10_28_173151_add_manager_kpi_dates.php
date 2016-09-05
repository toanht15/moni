<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerKpiDates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE manager_kpi_dates ALTER COLUMN status SET DEFAULT 0;');
		Schema::table('manager_kpi_dates', function(Blueprint $table)
		{
            $table->tinyInteger('brand_kpi_status')->default(0)->after('status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE manager_kpi_dates ALTER COLUMN status DROP DEFAULT;');
		Schema::table('manager_kpi_dates', function(Blueprint $table)
		{
            $table->dropColumn('brand_kpi_status');
		});
	}

}
