<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumn11 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $value = array(
            'name' => 'セグメントからのメッセージ送信数（当日)',
            'import' => 'jp.aainc.classes.manager_kpi.SegmentsCreatedSendingMessageSumKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'セグメントからのFB広告カスタムオーディエンス生成数（当日)',
            'import' => 'jp.aainc.classes.manager_kpi.SegmentsCreatedFBAdsCustomAudienceSumKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'セグメントからのメッセージ送信率（当日)',
            'import' => 'jp.aainc.classes.manager_kpi.CreateSendingMessageClientsRateKPI'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'セグメントからのFB広告カスタムオーディエンス生成率（当日)',
            'import' => 'jp.aainc.classes.manager_kpi.CreateFBAdsCustomAudienceClientsRateKPI'
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
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.SegmentsCreatedSendingMessageSumKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.SegmentsCreatedFBAdsCustomAudienceSumKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CreateSendingMessageClientsRateKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CreateFBAdsCustomAudienceClientsRateKPI')->delete();
    }

}
