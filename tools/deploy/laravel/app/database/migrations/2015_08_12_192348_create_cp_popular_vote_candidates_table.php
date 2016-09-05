<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpPopularVoteCandidatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('cp_popular_vote_candidates', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('cp_popular_vote_action_id');
			$table->string('title', 255)->default('');
			$table->text('description');
			$table->string('thumbnail_url', 511)->default('');
			$table->string('original_url', 511)->default('');
			$table->integer('order_no');
			$table->boolean('del_flg');
			$table->timestamps();

			$table->unique(array('id', 'cp_popular_vote_action_id'), 'id_and_cp_popular_vote_candidates_action_id_unique');
			$table->foreign('cp_popular_vote_action_id', 'popular_vote_action_relation')->references('id')->on('cp_popular_vote_actions');
			$table->index('order_no');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('cp_popular_vote_candidates');
	}

}
