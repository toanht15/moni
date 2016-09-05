<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SynCps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('syn_cps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cp_id')->unsigned();
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('cp_id')->references('id')->on('cps');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('syn_cps');
	}

}
