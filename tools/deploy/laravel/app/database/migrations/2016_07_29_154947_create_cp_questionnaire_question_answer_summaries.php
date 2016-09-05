<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpQuestionnaireQuestionAnswerSummaries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_questionnaire_question_answer_summaries', function(Blueprint $table)
		{
			$table->unsignedBigInteger('cp_action_id');
			$table->unsignedBigInteger('question_id');
			$table->unsignedBigInteger('question_choice_id');
			$table->unsignedBigInteger('n_answers');
			$table->timestamps();

			$table->primary(array('cp_action_id', 'question_id', 'question_choice_id'), 'cp_questionnaire_question_answer_summaries_primary');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_questionnaire_question_answer_summaries');
	}

}
