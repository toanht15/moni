<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiries', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('inquiry_user_id');
			$table->string('user_name', 50)->default('');
			$table->string('user_agent', 255)->default('');
			$table->string('referer', 255)->default('');
			$table->unsignedInteger('cp_id')->default(0);
			$table->unsignedInteger('brand_id')->default(0);
			$table->tinyInteger('category')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('inquiry_user_id')->references('id')->on('inquiry_users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiries');
	}

}
