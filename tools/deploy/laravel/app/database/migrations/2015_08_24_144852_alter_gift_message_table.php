<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGiftMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('gift_messages', function(Blueprint $table)
		{
			$table->dropForeign('gift_messages_coupon_code_id_foreign');
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
			$table->foreign('coupon_code_id')->references('id')->on('coupon_codes');
		});
	}

}
