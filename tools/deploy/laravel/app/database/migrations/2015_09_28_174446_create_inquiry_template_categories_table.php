<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryTemplateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiry_template_categories', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('inquiry_brand_id');
			$table->string('name', 50)->default('');
			$table->integer('order_no')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('inquiry_brand_id')->references('id')->on('inquiry_brands');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiry_template_categories');
	}

}
