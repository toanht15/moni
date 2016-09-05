<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpPageViewLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_page_view_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('cp_id');
			$table->tinyInteger("status");
			$table->tinyInteger('del_flg')->default(0);

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
		Schema::drop("cp_page_view_logs");
	}
}
