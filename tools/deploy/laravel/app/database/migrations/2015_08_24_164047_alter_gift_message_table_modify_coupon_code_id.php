<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGiftMessageTableModifyCouponCodeId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('gift_messages', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `gift_messages` MODIFY `coupon_code_id` INT(20) NOT NULL DEFAULT 0');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('gift_messages', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE `gift_messages` MODIFY `coupon_code_id` INT(20) UNSIGNED');
		});
	}

}
