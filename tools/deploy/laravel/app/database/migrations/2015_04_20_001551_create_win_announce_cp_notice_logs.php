<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinAnnounceCpNoticeLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('win_announce_cp_notice_logs', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->Integer('cp_user_id')->unsigned();
            $table->Integer('cp_action_id')->unsigned();
            $table->Integer('cp_msg_delivery_reserve_id')->unsigned();
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('cp_msg_delivery_reserve_id')->references('id')->on('cp_message_delivery_reservations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('win_announce_cp_notice_logs');
	}

}
