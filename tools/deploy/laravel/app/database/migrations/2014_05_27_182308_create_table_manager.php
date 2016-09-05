<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableManager extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('managers', function($t)
		{
			$t->increments('id');
			$t->string('name',255)->default('');
			$t->string('mail_address',255)->default('');
			$t->string('password',32)->default('');
			$t->dateTime('pw_register_date')->default('0000-00-00 00:00:00');
			$t->dateTime('pw_expire_date')->default('0000-00-00 00:00:00');
			$t->tinyInteger('pw_expire_mail_send_flg')->default(0);
			$t->tinyInteger('login_invalid_count')->default(0);
			$t->dateTime('login_try_reset_date')->default('0000-00-00 00:00:00');
			$t->dateTime('login_lockout_reset_date')->default('0000-00-00 00:00:00');
			$t->tinyInteger('del_flg')->default(0);
			// created_at, updated_at DATETIME
			$t->timestamps();
			$t->index('mail_address');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('managers');
	}

}
