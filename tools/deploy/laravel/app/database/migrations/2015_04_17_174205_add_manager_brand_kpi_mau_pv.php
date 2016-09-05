<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerBrandKpiMauPv extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => 'MAU',
                'import' => 'jp.aainc.classes.manager_brand_kpi.BrandsMAU',
            )
        );
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => 'PV',
                'import' => 'jp.aainc.classes.manager_brand_kpi.BrandsPV',
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
        DB::table('manager_brand_kpi_columns')->where('import', 'jp.aainc.classes.manager_brand_kpi.BrandsMAU')->delete();
        DB::table('manager_brand_kpi_columns')->where('import', 'jp.aainc.classes.manager_brand_kpi.BrandsPV')->delete();
    }

}
