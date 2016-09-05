<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileChoiceAnswerHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_choice_answer_histories', function(Blueprint $table)
		{
			// auto increment id (primary key)
			$table->bigIncrements('id');

			$table->integer('choice_id')->unsigned();
			$table->integer('questionnaires_questions_relation_id')->unsigned();
			$table->bigInteger('brands_users_relation_id')->unsigned();
			$table->string('answer_text')->default('');

			$table->boolean('del_flg')->default(false);
			$table->foreign('choice_id')->references('id')->on('profile_question_choices');
			$table->foreign('questionnaires_questions_relation_id', 'profile_free_answer_histories_questions_relation_id_foreign')->references('id')->on('profile_questionnaires_questions_relations');
			$table->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_choice_answer_histories');
	}

}
