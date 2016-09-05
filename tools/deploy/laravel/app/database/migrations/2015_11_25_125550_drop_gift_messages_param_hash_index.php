<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropGiftMessagesParamHashIndex extends Migration {

	public function up()
	{
		Schema::table('gift_messages', function(Blueprint $table)
		{
			$table->dropIndex("gift_messages_param_hash_index");
		});
	}

	public function down()
	{
		Schema::table('gift_messages', function(Blueprint $table)
		{
			$table->index("param_hash");
		});
	}

}
