<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLoginLogData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::create('login_log_data', function($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->bigInteger('user_id')->unsigned();
            $t->integer('brand_id')->unsigned();
            $t->dateTime('login_date');
            $t->string('cookie', 512)->default('');
            $t->string('user_agent', 512)->default('');
            $t->string('referer_url', 512)->default('');
            $t->tinyInteger('device')->default(1);
            $t->string('login_type', 11)->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->index('user_id');
            $t->foreign('brand_id')->references('id')->on('brands');
            $t->foreign('user_id')->references('id')->on('users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('login_log_data');
	}

}
