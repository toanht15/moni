<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAndUpdateInquiryBrandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inquiry_brands', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `inquiry_brands` ALTER `aa_alert_flg` SET DEFAULT 1');
			DB::statement('UPDATE `inquiry_brands` SET `aa_alert_flg` = 1');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inquiry_brands', function(Blueprint $table)
		{
			DB::statement('UPDATE `inquiry_brands` SET `aa_alert_flg` = 0');
			DB::statement('ALTER TABLE `inquiry_brands` ALTER `aa_alert_flg` SET DEFAULT 0');
		});
	}

}
