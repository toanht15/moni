<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLoginLogManagerData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('login_log_manager_data', function($t)
		{
			$t->increments('id');

			$t->string('mail_address',255)->default('');
			$t->dateTime('login_date')->default('0000-00-00 00:00:00');
			$t->string('cookie',512)->default('');
			$t->string('user_agent',512)->default('');
			$t->tinyInteger('device')->default(1);
			$t->string('ip_address',30)->default('');
			$t->tinyInteger('del_flg')->default(0);
			// created_at, updated_at DATETIME
			$t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('login_log_manager_data');
	}

}
