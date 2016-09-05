<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropInstagramEntriesObjectIdIndex extends Migration {

	public function up()
	{
		Schema::table('instagram_entries', function(Blueprint $table)
		{
			$table->dropIndex("instagram_entries_object_id_index");
		});
	}

	public function down()
	{
		Schema::table('instagram_entries', function(Blueprint $table)
		{
			$table->index("object_id");
		});
	}

}
