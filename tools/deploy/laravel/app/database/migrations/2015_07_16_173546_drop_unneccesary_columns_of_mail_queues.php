<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnneccesaryColumnsOfMailQueues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mail_queues', function(Blueprint $table)
		{
			$table->dropColumn('charset');
			$table->dropColumn('real_charset');
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
			$table->string('charset', 20)->default('ISO-2022-JP'); // 念のため
			$table->string('real_charset', 20)->default('ISO-2022-JP-MS'); // 念のため
		});
	}

}
