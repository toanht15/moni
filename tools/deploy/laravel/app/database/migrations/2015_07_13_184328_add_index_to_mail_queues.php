<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToMailQueues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mail_queues', function(Blueprint $table)
		{
			$table->index(array('send_schedule'), 'mail_queues_send_schedule_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mail_queues', function(Blueprint $table)
		{
			$table->dropIndex(array('send_schedule'), 'mail_queues_send_schedule_index');
		});
	}

}
