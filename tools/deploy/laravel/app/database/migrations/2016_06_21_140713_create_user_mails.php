<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_mails', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->unsignedBigInteger('user_id');
			$table->datetime('sent_at');
			$table->timestamps();
			$table->index('sent_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_mails');
	}

}
