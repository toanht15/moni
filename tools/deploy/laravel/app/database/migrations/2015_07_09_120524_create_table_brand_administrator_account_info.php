<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandAdministratorAccountInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_administrator_account_info', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('brand_id')->unsigned();
			$table->tinyInteger('administrator_account_no')->unsigned();
			$table->string('name',255)->default('');
			$table->string('mail_address',255)->default('');
			$table->string('tel_no1',255)->default('');
			$table->string('tel_no2',255)->default('');
			$table->string('tel_no3',255)->default('');
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
		Schema::drop('brand_administrator_account_info');
	}

}
