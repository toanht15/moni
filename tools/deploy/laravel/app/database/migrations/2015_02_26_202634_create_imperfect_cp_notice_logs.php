<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImperfectCpNoticeLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('imperfect_cp_notice_logs', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->Integer('cp_user_id')->unsigned();
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('imperfect_cp_notice_logs');
	}

}
