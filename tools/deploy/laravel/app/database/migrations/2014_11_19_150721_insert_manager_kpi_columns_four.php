<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumnsFour extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'ファン累計',
                'import' => 'jp.aainc.classes.manager_kpi.BrandsUsersNum',
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
        DB::table('manager_kpi_columns')->where('name', 'ファン累計')->delete();
	}

}
