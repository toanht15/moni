<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionChoicesAnswerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_question_choice_answers', function(Blueprint $t)
		{
			// auto increment id (primary key)
			$t->bigIncrements('id');

			$t->integer('choice_id')->unsigned();
			$t->integer('questionnaires_questions_relation_id')->unsigned();
			$t->bigInteger('brands_users_relation_id')->unsigned();
			$t->string('answer_text')->default('');

			$t->boolean('del_flg')->default(false);
			// created_at, updated_at DATETIME
			$t->timestamps();
			$t->index('choice_id');
			$t->index('questionnaires_questions_relation_id', 'profile_question_choice_answer_key');
			$t->index('brands_users_relation_id');
			$t->index(array('questionnaires_questions_relation_id', 'brands_users_relation_id'), 'profile_questionnaires_questions_choice_relation_multi_index');

			$t->foreign('choice_id', 'profile_choice_answers_foreign_key')->references('id')->on('profile_question_choices');
			$t->foreign('questionnaires_questions_relation_id', 'profile_questionnaires_questions_choice_relation_foreign_key')->references('id')->on('profile_questionnaires_questions_relations');
			$t->foreign('brands_users_relation_id', 'brands_users_relation_choice_foreign_key')->references('id')->on('brands_users_relations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_question_choice_answers');
	}

}
