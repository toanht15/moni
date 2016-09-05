<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBrandsUsersRelationsScoreIndex extends Migration {

	public function up()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			$table->dropIndex("brands_users_relations_score_index");
		});
	}

	public function down()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			$table->index("score");
		});
	}

}
