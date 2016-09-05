<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManagerKpiColumnSeven extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => '公開企業数',
                'import' => 'jp.aainc.classes.manager_kpi.CompanyPublicKPI',
            )
        );

        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => '公開キャンペーン数',
                'import' => 'jp.aainc.classes.manager_kpi.OpenCampaignKPI',
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
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CompanyPublicKPI')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.OpenCampaignKPI')->delete();
    }

}
