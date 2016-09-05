<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionFreeAnswerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_question_free_answers', function(Blueprint $t)
		{
			// auto increment id (primary key)
			$t->bigIncrements('id');

			$t->integer('questionnaires_questions_relation_id')->unsigned();
			$t->bigInteger('brands_users_relation_id')->unsigned();
			$t->string('answer_text')->default('');

			$t->boolean('del_flg')->default(false);
			// created_at, updated_at DATETIME
			$t->timestamps();
			$t->index('questionnaires_questions_relation_id', 'profile_question_free_answer_key');
			$t->index('brands_users_relation_id');

			$t->foreign('questionnaires_questions_relation_id', 'profile_question_free_answers_foreign_key')->references('id')->on('profile_questionnaires_questions_relations');
			$t->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_question_free_answers');
	}

}
