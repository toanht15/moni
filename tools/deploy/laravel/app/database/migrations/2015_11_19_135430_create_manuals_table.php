<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manuals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 255)->default('');
			$table->text('url');
			$table->tinyInteger('type')->default(0);
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
		Schema::drop('manuals');
	}

}
