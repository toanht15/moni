<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('update_count_batch_logs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('batch_name');
            $table->tinyInteger('status');
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
		Schema::drop('update_count_batch_logs');
	}

}
