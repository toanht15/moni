<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'パネルクリック数',
                'import' => 'jp.aainc.classes.manager_kpi.UserPanelClickNumKPI',
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
        DB::table('manager_kpi_columns')->where('name', 'パネルクリック数')->delete();
	}

}
