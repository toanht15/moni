<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHtmlContentForGiftCouponConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('gift_coupon_configs', function(Blueprint $table)
		{
            $table->longText('html_content')->default('')->after('message');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('gift_coupon_configs', function(Blueprint $table)
		{
            $table->dropColumn('html_content');
		});
	}

}
