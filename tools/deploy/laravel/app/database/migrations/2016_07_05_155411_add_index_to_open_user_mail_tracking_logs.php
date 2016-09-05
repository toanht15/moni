<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToOpenUserMailTrackingLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('open_user_mail_tracking_logs', function(Blueprint $table)
		{
			$table->index('user_mail_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('open_user_mail_tracking_logs', function(Blueprint $table)
		{
			$table->dropIndex('open_user_mail_tracking_logs_user_mail_id_index');
		});
	}

}
