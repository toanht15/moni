<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumnsFive extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'MAU(UU)',
                'import' => 'jp.aainc.classes.manager_kpi.MAUUU',
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
        DB::table('manager_kpi_columns')->where('name', 'MAU(UU)')->delete();
	}

}
