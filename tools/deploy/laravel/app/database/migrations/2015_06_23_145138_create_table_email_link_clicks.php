<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEmailLinkClicks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('clicked_email_link_logs', function(Blueprint $t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->integer('brand_id')->unsigned();
            $t->bigInteger('user_id')->unsigned();
            $t->integer('cp_action_id')->unsigned();
            $t->integer('click_count')->default(0);
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
            $t->foreign('brand_id')->references('id')->on('brands');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('clicked_email_link_logs');
	}

}
