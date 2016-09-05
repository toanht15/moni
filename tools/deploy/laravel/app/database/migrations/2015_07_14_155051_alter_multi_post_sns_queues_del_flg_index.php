<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMultiPostSnsQueuesDelFlgIndex extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('multi_post_sns_queues', function(Blueprint $table)
		{
			$table->index('del_flg');
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
            $table->dropIndex('multi_post_sns_queues_del_flg_index');
		});
	}

}
