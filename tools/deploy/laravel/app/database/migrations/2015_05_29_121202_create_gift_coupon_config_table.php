<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftCouponConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gift_coupon_configs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_gift_action_id')->unsigned();
            $table->bigInteger('coupon_id')->unsigned();
            $table->longText('message')->default('');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_gift_action_id');
            $table->index('coupon_id');
            $table->foreign('cp_gift_action_id')->references('id')->on('cp_gift_actions');
            $table->foreign('coupon_id')->references('id')->on('coupons');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gift_coupon_configs');
	}

}
