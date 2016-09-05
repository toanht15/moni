<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerBrandKpi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => '日別ファン増加数',
                'import' => 'jp.aainc.classes.manager_brand_kpi.DailyBrandsUsersNum',
            ));
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => '累計ファン数',
                'import' => 'jp.aainc.classes.manager_brand_kpi.BrandsUsersNum',
            ));

    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::table('manager_brand_kpi_columns')->where('import', 'jp.aainc.classes.manager_brand_kpi.DailyBrandsUsersNum')->delete();
        DB::table('manager_brand_kpi_columns')->where('import', 'jp.aainc.classes.manager_brand_kpi.BrandsUsersNum')->delete();
	}

}
