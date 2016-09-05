<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopularVoteUserSharesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('popular_vote_user_shares', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('popular_vote_user_id');
			$table->tinyInteger('social_media_type')->default(0);
			$table->string('share_text', 511)->default('');
			$table->tinyInteger('execute_status')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->unique(array('popular_vote_user_id', 'social_media_type'), 'popular_vote_user_shares_user_id_social_media_type_unique');
			$table->foreign('popular_vote_user_id', 'popular_vote_user_relation')->references('id')->on('popular_vote_users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('popular_vote_user_share');
	}

}
