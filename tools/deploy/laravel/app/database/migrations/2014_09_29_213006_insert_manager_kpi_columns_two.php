<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumnsTwo extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'cp参加純増数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinCpUserNumKPI',
            ));
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'MPCPから新規登録純増数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinMoniplaCpRelationNumKPI',
            ));
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'MPCPから新規登録延べ数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinMoniplaCpRelationRunningNumKPI',
            ));
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'BRANDCoから新規登録純増数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinBrandcoCpRelationNumKPI',
            ));
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'BRANDCoから新規登録延べ数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinBrandcoCpRelationRunningNumKPI',
            ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinCpUserNumKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinMoniplaCpRelationNumKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinMoniplaCpRelationRunningNumKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinBrandcoCpRelationNumKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.JoinBrandcoCpRelationRunningNumKPI')->delete();
    }

}
