<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_info', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('cp_id')->unsigned();
			$table->string('salesforce_id',255)->default('');
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
		Schema::drop('cp_info');
	}

}
