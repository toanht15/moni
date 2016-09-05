<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryRoomsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiry_rooms', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('inquiry_brand_id');
			$table->unsignedInteger('inquiry_id');
			$table->string('operator_name', 50)->default('');
			$table->tinyInteger('operator_type')->default(1);
			$table->tinyInteger('status')->default(1);
			$table->string('access_token', 255)->default('');
			$table->unsignedInteger('inquiry_section_id_1')->default(0);
			$table->unsignedInteger('inquiry_section_id_2')->default(0);
			$table->unsignedInteger('inquiry_section_id_3')->default(0);
			$table->text('remarks');
			$table->boolean('forwarded_flg')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('inquiry_brand_id')->references('id')->on('inquiry_brands');
			$table->foreign('inquiry_id')->references('id')->on('inquiries');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiry_rooms');
	}

}

