<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAndDropIndexOfMailQueues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mail_queues', function(Blueprint $table)
		{
			$table->dropIndex(array('send_schedule'), 'mail_queues_send_schedule_index');
			$table->index(array('del_flg'), 'mail_queues_del_flg_index');
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
			$table->index(array('send_schedule'), 'mail_queues_send_schedule_index');
			$table->dropIndex(array('del_flg'), 'mail_queues_del_flg_index');
		});
	}

}
