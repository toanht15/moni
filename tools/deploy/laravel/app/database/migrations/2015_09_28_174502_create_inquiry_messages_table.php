<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiry_messages', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('inquiry_id');
			$table->tinyInteger('sender')->default(0);
			$table->text('content')->default('');
			$table->tinyInteger('draft_flg')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('inquiry_id')->references('id')->on('inquiries');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiry_messages');
	}

}
