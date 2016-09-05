<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMultiPostSnsQueuesApiResult extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('multi_post_sns_queues', function(Blueprint $table)
		{
			$table->text('api_result')->after('error_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('multi_post_sns_queues', function(Blueprint $table)
		{
			$table->dropColumn('api_result');
		});
	}

}
