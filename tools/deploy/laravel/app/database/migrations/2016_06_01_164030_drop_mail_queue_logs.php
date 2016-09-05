<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropMailQueueLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('mail_queue_logs_0');
		Schema::drop('mail_queue_logs_1');
		Schema::drop('mail_queue_logs_2');
		Schema::drop('mail_queue_logs_3');
		Schema::drop('mail_queue_logs_4');
		Schema::drop('mail_queue_logs_5');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
