<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumnNine extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $value = array(
            'name' => 'キャンペーン参加数（アカウント入力完了）',
            'import' => 'jp.aainc.classes.manager_kpi.JoinCpEntryUserKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'キャンペーン参加UU（当日）',
            'import' => 'jp.aainc.classes.manager_kpi.JoinCpUserUUKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'キャンペーン参加MAU（30日移動）',
            'import' => 'jp.aainc.classes.manager_kpi.JoinCpUserMAUBetween30DaysKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'キャンペーン参加UU（当月積上）',
            'import' => 'jp.aainc.classes.manager_kpi.JoinCpUserMAUKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinCpEntryUserKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinCpUserUUKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinCpUserMAUBetween30DaysKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinCpUserMAUKPI')->delete();
	}

}
