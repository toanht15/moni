<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConversionKpiToManagerKpiColumnsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'コンバージョン数',
                'import' => 'jp.aainc.classes.manager_kpi.ConversionNum',
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
        DB::table('manager_kpi_columns')->where('name', 'コンバージョン数')->delete();
    }

}
