<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopularVoteUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('popular_vote_users', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('cp_action_id');
			$table->unsignedInteger('cp_user_id');
			$table->unsignedInteger('cp_popular_vote_candidate_id');
			$table->boolean('del_flg');
			$table->timestamps();

			$table->unique(array('cp_action_id', 'cp_user_id'));
			$table->foreign('cp_action_id', 'popular_vote_users_action_relation')->references('id')->on('cp_actions');
			$table->foreign('cp_user_id', 'popular_vote_users_user_relation')->references('id')->on('cp_users');
			$table->foreign('cp_popular_vote_candidate_id', 'popular_vote_users_candidate_relation')->references('id')->on('cp_popular_vote_candidates');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('popular_vote_users');
	}

}
