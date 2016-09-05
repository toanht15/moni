<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpAlertMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_alert_mails', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->tinyInteger('now_announce_flg')->default(0);
            $table->tinyInteger('passed_announce_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('cp_id')->references('id')->on('cps');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cp_alert_mails');
	}

}
