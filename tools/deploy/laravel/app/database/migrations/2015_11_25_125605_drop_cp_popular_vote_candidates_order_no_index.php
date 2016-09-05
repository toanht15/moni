<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCpPopularVoteCandidatesOrderNoIndex extends Migration {

	public function up()
	{
		Schema::table('cp_popular_vote_candidates', function(Blueprint $table)
		{
			$table->dropIndex("cp_popular_vote_candidates_order_no_index");
		});
	}

	public function down()
	{
		Schema::table('cp_popular_vote_candidates', function(Blueprint $table)
		{
			$table->index("order_no");
		});
	}

}
