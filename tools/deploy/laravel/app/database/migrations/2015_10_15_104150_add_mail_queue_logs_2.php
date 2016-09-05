<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailQueueLogs2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mail_queue_logs_2', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('send_schedule')->default('0000-00-00 00:00:00');
			$table->string('to_address', 255)->default('');
			$table->string('cc_address', 255)->default('');
			$table->string('bcc_address', 255)->default('');
			$table->string('subject', 255)->default('');
			$table->text('body_plain');
			$table->text('body_html');
			$table->string('from_address', 255)->default('');
			$table->string('envelope', 255)->default('');
			$table->bigInteger('user_id')->nullable();
			$table->integer('cp_message_delivery_reservation_id')->nullable();
			$table->dateTime('created_at_log');
			$table->dateTime('updated_at_log');
			$table->timestamps();
			$table->unique(array('user_id', 'cp_message_delivery_reservation_id'), 'mql2_ui1');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mail_queue_logs_2', function(Blueprint $table)
		{
			$table->drop();
		});
	}

}
