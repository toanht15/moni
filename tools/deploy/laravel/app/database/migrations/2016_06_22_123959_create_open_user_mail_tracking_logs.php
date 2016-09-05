<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenUserMailTrackingLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('open_user_mail_tracking_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedBigInteger('user_mail_id');
			$table->string('user_agent', 512)->default('');
			$table->string('remote_ip',255)->default('');
			$table->string('referer_url', 512)->default('');
			$table->tinyInteger('device')->default(1);
			$table->string('language',255)->default('');
			$table->tinyInteger('del_flg')->default(0);
			$table->datetime('opened_at');
			$table->timestamps();
			$table->index('opened_at');
		});
	}

	/**
	 * Reverse the migrations. FF
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('open_user_mail_tracking_logs');
	}

}
