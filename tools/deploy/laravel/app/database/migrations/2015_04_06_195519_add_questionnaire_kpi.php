<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionnaireKpi extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'プロフィールアンケート数',
                'import' => 'jp.aainc.classes.manager_kpi.ProfileQuestionnaireKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'プロフィールアンケート設問数',
                'import' => 'jp.aainc.classes.manager_kpi.ProfileQuestionKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'プロフィールアンケート単一回答数',
                'import' => 'jp.aainc.classes.manager_kpi.ProfileQuestionSingleAnswerKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'プロフィールアンケート複数回答人数',
                'import' => 'jp.aainc.classes.manager_kpi.ProfileQuestionMultiAnswerPersonKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'プロフィールアンケート複数回答数',
                'import' => 'jp.aainc.classes.manager_kpi.ProfileQuestionMultiAnswerKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'プロフィールアンケート自由回答数',
                'import' => 'jp.aainc.classes.manager_kpi.ProfileQuestionFreeAnswerKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'CPアンケート数',
                'import' => 'jp.aainc.classes.manager_kpi.CpQuestionnaireKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'CPアンケート設問数',
                'import' => 'jp.aainc.classes.manager_kpi.CpQuestionKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'CPアンケート単一回答数',
                'import' => 'jp.aainc.classes.manager_kpi.CpQuestionSingleAnswerKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'CPアンケート複数回答人数',
                'import' => 'jp.aainc.classes.manager_kpi.CpQuestionMultiAnswerPersonKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'CPアンケート複数回答数',
                'import' => 'jp.aainc.classes.manager_kpi.CpQuestionMultiAnswerKPI',
            )
        );
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'CPアンケート自由回答数',
                'import' => 'jp.aainc.classes.manager_kpi.CpQuestionFreeAnswerKPI',
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
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.ProfileQuestionnaireKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.ProfileQuestionKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.ProfileQuestionSingleAnswerKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.ProfileQuestionMultiAnswerPersonKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.ProfileQuestionMultiAnswerKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.ProfileQuestionFreeAnswerKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CpQuestionnaireKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CpQuestionKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CpQuestionSingleAnswerKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CpQuestionMultiAnswerPersonKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CpQuestionMultiAnswerKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CpQuestionFreeAnswerKPI')->delete();
    }

}
