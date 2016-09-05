<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionChoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_question_choices', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('question_id')->unsigned();
			$t->integer('choice_num')->default(0);
			$t->string('choice', 512)->defaule('');
			$t->boolean('other_choice_flg')->default(false);

			$t->index('question_id');
			$t->boolean('del_flg')->default(false);
			$t->timestamps();
			$t->foreign('question_id', 'profile_question_choices_foreign_key')->references('id')->on('profile_questionnaire_questions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_question_choices');
	}

}
