<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecordTimeToProfileFreeAnswerHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('profile_free_answer_histories', function(Blueprint $table)
		{
			$table->timestamp('submitted_at')->after('answer_text');;
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('profile_free_answer_histories', function(Blueprint $table)
		{
			$table->dropColumn('submitted_at');
		});
	}

}
