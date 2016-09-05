<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiry_users', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedBigInteger('user_id')->default(0);
			$table->string('mail_address', 255)->default('');
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->index('user_id');
			$table->index('mail_address');
			$table->unique(array('user_id', 'mail_address'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiry_users');
	}

}
