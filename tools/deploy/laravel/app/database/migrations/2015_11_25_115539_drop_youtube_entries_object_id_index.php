<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropYoutubeEntriesObjectIdIndex extends Migration {

	public function up()
	{
		Schema::table('youtube_entries', function(Blueprint $table)
		{
			$table->dropIndex("youtube_entries_object_id_index");
		});
	}

	public function down()
	{
		Schema::table('youtube_entries', function(Blueprint $table)
		{
			$table->index("object_id");
		});
	}

}
