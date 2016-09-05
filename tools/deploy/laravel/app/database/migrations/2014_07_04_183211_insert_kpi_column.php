<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertKpiColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        //手入力用のサンプルカラム
        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => '売上',
                'import' => '',
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
        DB::table('manager_kpi_columns')->where('name', '売上')->delete();
	}

}
