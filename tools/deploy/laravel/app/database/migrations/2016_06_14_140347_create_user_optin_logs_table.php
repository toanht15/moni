<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOptinLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_optin_logs', function(Blueprint $table)
		{
			$table->increments('id');

            $table->unsignedInteger('platform_user_id');
            $table->boolean('optin')->default(0);
            $table->unsignedInteger('from_id');
            $table->unsignedInteger('free_item');
            $table->timestamp('created_at');
			
			$table->index('platform_user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_optin_logs');
	}

}
