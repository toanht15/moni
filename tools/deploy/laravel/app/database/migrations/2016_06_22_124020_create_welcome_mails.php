<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWelcomeMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('welcome_mails', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedBigInteger('user_mail_id');
			$table->unsignedInteger('brand_id');
			$table->unsignedInteger('cp_id')->nullable();
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
		Schema::drop('welcome_mails');
	}

}
