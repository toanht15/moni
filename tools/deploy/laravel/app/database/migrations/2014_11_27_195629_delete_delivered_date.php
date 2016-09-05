<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteDeliveredDate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_message_delivery_targets', function(Blueprint $table)
		{
            $table->dropColumn('delivered_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_message_delivery_targets', function(Blueprint $table)
		{
            $table->dateTime('delivered_date')->default('0000-00-00 00:00:00')->after('cp_action_id');
		});
	}

}
