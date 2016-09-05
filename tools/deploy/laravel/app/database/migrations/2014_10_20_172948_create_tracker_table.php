<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('conversions', function(Blueprint $t)
        {
            $t->increments('id');
            $t->integer('brand_id')->unsigned();
            $t->string('name',255)->default('');
            $t->text('description');
            $t->tinyInteger('del_flg')->default(0);
            $t->foreign('brand_id')->references('id')->on('brands');
            // created_at, updated_at DATETIME
            $t->timestamps();
        });

        Schema::create('brands_users_conversions', function(Blueprint $t)
        {
            $t->bigIncrements('id');
            $t->bigInteger('user_id')->unsigned();
            $t->integer('brand_id')->unsigned();
            $t->integer('conversion_id')->unsigned();
            $t->dateTime('date_conversion')->default('0000-00-00 00:00:00');
            $t->string('order_no',255)->default('');
            $t->string('order_price',255)->default('');
            $t->string('order_count',255)->default('');
            $t->string('remote_address',255)->default('');
            $t->string('remote_host',255)->default('');
            $t->string('user_agent',255)->default('');
            $t->string('language',255)->default('');
            $t->string('free1', 255)->default('');
            $t->string('free2', 255)->default('');
            $t->string('free3', 255)->default('');
            $t->string('free4', 255)->default('');
            $t->tinyInteger('del_flg')->default(0);
            $t->index(array('user_id', 'brand_id', 'conversion_id'));
            $t->foreign('brand_id')->references('id')->on('brands');
            $t->foreign('user_id')->references('id')->on('users');
            $t->foreign('conversion_id')->references('id')->on('conversions');
            // created_at, updated_at DATETIME
            $t->timestamps();
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('brands_users_conversions');
        Schema::drop('conversions');
	}

}
