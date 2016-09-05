<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionChoiceRequirementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_question_choice_requirements', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('question_id')->unsigned();
			$t->boolean('use_other_choice_flg')->default(false);
			$t->boolean('random_order_flg')->defaule(false);
			$t->boolean('multi_answer_flg')->default(false);

			$t->index('question_id', 'profile_question_choice_requirement_key');
			$t->boolean('del_flg')->default(false);
			$t->timestamps();
			$t->foreign('question_id', 'profile_question_foreign_key')->references('id')->on('profile_questionnaire_questions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_question_choice_requirements');
	}

}
