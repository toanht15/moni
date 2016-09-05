<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTwitterEntriesObjectIdIndex extends Migration {

	public function up()
	{
		Schema::table('twitter_entries', function(Blueprint $table)
		{
			$table->dropIndex("twitter_entries_object_id_index");
		});
	}

	public function down()
	{
		Schema::table('twitter_entries', function(Blueprint $table)
		{
			$table->index("object_id");
		});
	}

}
