<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerKpiDates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_kpi_dates', function(Blueprint $table)
		{
			$table->increments('id');
            $table->date('summed_date');
            $table->tinyInteger('status');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_kpi_dates');
	}

}
