<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumn10 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $value = array(
            'name' => '条件セグメント生成数（当日）',
            'import' => 'jp.aainc.classes.manager_kpi.CountConditionalSegments'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'セグメントグループ生成数（当日）',
            'import' => 'jp.aainc.classes.manager_kpi.CountGroupSegments'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'セグメント生成率',
            'import' => 'jp.aainc.classes.manager_kpi.CountBrandCreatedSegments'
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
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CountConditionalSegments')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CountGroupSegments')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CountBrandCreatedSegments')->delete();
    }

}
