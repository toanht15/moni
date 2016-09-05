<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResendCpUserStatusLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resend_cp_user_status_logs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->bigInteger('monipla_user_id')->unsigned();
            $table->tinyInteger('app_id');
            $table->Integer('cp_id');
            $table->Integer('module_type');
            $table->boolean('send_flg')->default(0);
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
		Schema::drop('resend_cp_user_status_logs');
	}

}
