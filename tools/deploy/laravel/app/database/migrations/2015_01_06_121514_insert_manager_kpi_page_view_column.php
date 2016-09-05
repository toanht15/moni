<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiPageViewColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('manager_kpi_columns')->insert(
			array(
				'name' => 'PV',
				'import' => 'jp.aainc.classes.manager_kpi.PV',
			)
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('manager_kpi_columns')->where('name', 'PV')->delete();
	}

}
