<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleAndThumbnailImgCpUserActionMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_user_action_messages', function(Blueprint $table)
		{
            $table->string('title')->default("")->after('cp_action_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_user_action_messages', function (Blueprint $table) {
            $table->dropColumn("title");
		});
	}

}
