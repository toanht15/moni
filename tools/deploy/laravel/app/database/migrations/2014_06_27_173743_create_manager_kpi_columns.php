<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerKpiColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_kpi_columns', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name',255)->default('');
            $table->text('import');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
		});

        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'ユーザー総数',
                'import' => 'jp.aainc.classes.manager_kpi.UserNumKPI',
            )
        );

        DB::table('manager_kpi_columns')->insert(
            array(
                'name' => 'ユーザー純増数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinUserNumKPI',
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
		Schema::drop('manager_kpi_columns');
	}

}
