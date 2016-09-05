<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteProfileQuestionForeignKey extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('profile_questionnaire_answers', function (Blueprint $table) {
			$table->dropForeign('answer_question_foreign');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('profile_questionnaire_answers', function (Blueprint $table) {
			$table->foreign('question_id', 'answer_question_foreign')->references('id')->on('profile_questionnaires');
		});
	}

}
