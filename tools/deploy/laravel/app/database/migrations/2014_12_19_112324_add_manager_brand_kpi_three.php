<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerBrandKpiThree extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => 'オプトイン数',
                'import' => 'jp.aainc.classes.manager_brand_kpi.BrandsOptInFlgKPI',
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
        DB::table('manager_brand_kpi_columns')->where('import', 'jp.aainc.classes.manager_brand_kpi.BrandsOptInFlgKPI')->delete();
    }

}
