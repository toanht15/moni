<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTwEntriesUsersRetweets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('tw_entries_users_retweets', function (Blueprint $table) {
			$table->increments('id');

			$table->string('tw_uid');
			$table->bigInteger('object_id');
			$table->bigInteger('entry_object_id');
			$table->text('text');
			$table->timestamps();
			$table->index('tw_uid');
			$table->index('object_id');
			$table->index('entry_object_id');
			$table->unique(array('tw_uid', 'object_id'), 'tw_entries_users_retweets_unique_key');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('tw_entries_users_retweets');
	}

}
