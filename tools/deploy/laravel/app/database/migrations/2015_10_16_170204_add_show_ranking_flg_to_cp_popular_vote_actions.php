<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowRankingFlgToCpPopularVoteActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('cp_popular_vote_actions', function(Blueprint $table) {
			$table->boolean('show_ranking_flg')->default(1)->after('random_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_popular_vote_actions', function(Blueprint $table) {
			$table->dropColumn('show_ranking_flg');
		});
	}

}
