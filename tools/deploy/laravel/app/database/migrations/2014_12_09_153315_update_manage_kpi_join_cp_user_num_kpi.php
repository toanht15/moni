<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateManageKpiJoinCpUserNumKpi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('
        update manager_kpi_columns
        set name = "cp参加"
        where import ="jp.aainc.classes.manager_kpi.JoinCpUserNumKPI"
        ');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('
        update manager_kpi_columns
        set name = "cp参加純増数"
        where import ="jp.aainc.classes.manager_kpi.JoinCpUserNumKPI"
        ');
	}

}
