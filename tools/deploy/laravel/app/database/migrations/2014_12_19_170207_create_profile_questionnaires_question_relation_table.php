<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionnairesQuestionRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_questionnaires_questions_relations', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('brand_id')->unsigned();
			$t->integer('question_id')->unsigned();
			$t->boolean('requirement_flg')->default(false);
			$t->integer('number')->defaule(0);
			$t->boolean('public')->default(false);

			$t->index('question_id', 'profile_questionnaires_questions_relation_key');
			$t->index('brand_id');
			$t->boolean('del_flg')->default(false);
			$t->timestamps();
			$t->foreign('brand_id')->references('id')->on('brands');
			$t->foreign('question_id', 'new_profile_question_foreign_key')->references('id')->on('profile_questionnaire_questions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_questionnaires_questions_relations');
	}

}
