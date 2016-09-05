<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerKpiValues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_kpi_values', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('column_id')->unsigned();
            $table->date('summed_date');
            $table->string('value', 255);
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->unique(array('column_id', 'summed_date'));
            $table->foreign('column_id')->references('id')->on('manager_kpi_columns');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_kpi_values');
	}

}
