<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryBrandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiry_brands', function(Blueprint $table) {
			$table->increments('id');
			$table->BigInteger('brand_id');
			$table->boolean('aa_alert_flg')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->unique('brand_id');
		});

		DB::statement("INSERT INTO `inquiry_brands` (brand_id) VALUES (" . -1 . ")");
		DB::statement("INSERT INTO `inquiry_brands` SELECT null as id, id as brand_id, 0 as aa_alert_flg, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiry_brands');
	}

}
