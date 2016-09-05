<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpPageViews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_page_views', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('cp_id');
			$table->date("summed_date");
			$table->tinyInteger("type");
			$table->integer("total_view_count");
			$table->integer("pc_view_count");
			$table->integer("sp_view_count");
			$table->integer("tablet_view_count");
			$table->integer("user_count");
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
		Schema::drop("cp_page_views");
	}

}
