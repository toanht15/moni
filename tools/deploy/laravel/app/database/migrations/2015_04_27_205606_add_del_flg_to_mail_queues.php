<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDelFlgToMailQueues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mail_queues', function(Blueprint $table)
		{
			$table->boolean("del_flg")->default(false)->after("envelope");
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
			$table->dropColumn("del_flg");
		});
	}

}
