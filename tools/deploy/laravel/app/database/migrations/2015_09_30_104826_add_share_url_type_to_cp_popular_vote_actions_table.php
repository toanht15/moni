<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShareUrlTypeToCpPopularVoteActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('cp_popular_vote_actions', function(Blueprint $table) {
			$table->tinyInteger('share_url_type')->default(1)->after('share_placeholder');
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
			$table->dropColumn('share_url_type');
		});
	}

}
