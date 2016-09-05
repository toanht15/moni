<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoupons extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('coupons', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->integer('brand_id')->unsigned();
            $table->string('name', 255)->default('');
            $table->text('description');
            $table->integer('reserved_num')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('coupon_codes', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->bigInteger('coupon_id')->unsigned();
            $table->string('code', 255)->default('');
            $table->integer('max_num')->default(0);
            $table->integer('reserved_num')->default(0);
            $table->dateTime('expire_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('coupon_id')->references('id')->on('coupons');
        });

        Schema::create('coupon_code_users', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->bigInteger('coupon_code_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('used_flg')->default(0);
            $table->dateTime('used_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::create('cp_coupon_actions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('title', 255);
            $table->string('image_url', 512)->default('');
            $table->text('text');
            $table->bigInteger('coupon_id')->unsigned()->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('coupon_code_users');
        Schema::drop('cp_coupon_actions');
        Schema::drop('coupon_codes');
        Schema::drop('coupons');
	}

}
