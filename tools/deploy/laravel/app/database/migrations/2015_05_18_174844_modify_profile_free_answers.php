<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProfileFreeAnswers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('profile_question_free_answers', function(Blueprint $table)
		{
			$table->dropColumn('present_flg');
			$table->dropColumn('history_no');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('profile_question_free_answers', function(Blueprint $table)
		{
			$table->boolean('present_flg')->default(1)->after('answer_text');
			$table->smallInteger('history_no')->default(1)->after('answer_text');
		});
	}

}
