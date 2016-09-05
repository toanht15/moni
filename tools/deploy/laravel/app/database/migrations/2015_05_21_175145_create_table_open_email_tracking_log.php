<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOpenEmailTrackingLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('open_email_tracking_logs', function(Blueprint $t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->bigInteger('user_id')->unsigned();
            $t->integer('cp_action_id')->unsigned();
            $t->string('user_agent', 512)->default('');
            $t->string('remote_ip',255)->default('');
            $t->string('referer_url', 512)->default('');
            $t->tinyInteger('device')->default(1);
            $t->string('language',255)->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('cp_action_id', 'user_id'));
            $t->foreign('user_id')->references('id')->on('users');
            $t->foreign('cp_action_id')->references('id')->on('cp_actions');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('open_email_tracking_logs');
	}

}
