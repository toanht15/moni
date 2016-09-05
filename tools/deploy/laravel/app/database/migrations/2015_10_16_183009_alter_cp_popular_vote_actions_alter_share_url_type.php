<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpPopularVoteActionsAlterShareUrlType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('cp_popular_vote_actions', function(Blueprint $table) {
			DB::statement('ALTER TABLE `cp_popular_vote_actions` ALTER `share_url_type` SET DEFAULT 2');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('cp_popular_vote_actions', function(Blueprint $table) {
			DB::statement('ALTER TABLE `cp_popular_vote_actions` ALTER `share_url_type` SET DEFAULT 1');
		});
	}

}
