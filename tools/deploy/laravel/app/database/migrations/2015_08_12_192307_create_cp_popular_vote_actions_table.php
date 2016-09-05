<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpPopularVoteActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('cp_popular_vote_actions', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('cp_action_id');
			$table->string('title', 255)->default('');
			$table->string('image_url', 511)->default('');
			$table->text('text');
			$table->text('html_content');
			$table->tinyInteger('file_type')->default(1);
			$table->string('button_label_text', 255)->default('投票する');
			$table->boolean('fb_share_required')->default(0);
			$table->boolean('tw_share_required')->default(0);
			$table->string('share_placeholder', 511)->default('');
			$table->boolean('random_flg')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->unique('cp_action_id');
			$table->foreign('cp_action_id', 'cp_action_relation')->references('id')->on('cp_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
        Schema::drop('cp_popular_vote_actions');
	}

}
