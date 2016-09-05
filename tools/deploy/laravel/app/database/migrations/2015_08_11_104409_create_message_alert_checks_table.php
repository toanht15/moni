<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageAlertChecksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_alert_checks', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_message_delivery_reservation_id');
            $table->integer('count');
            $table->boolean('del_flg')->default(0);
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
		Schema::drop('message_alert_checks');
	}

}
