<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_questionnaire_questions', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('type_id')->unsigned();
			$t->text('question');

			$t->index('type_id');
			$t->boolean('del_flg')->default(false);
			$t->timestamps();
			$t->foreign('type_id')->references('id')->on('question_types');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_questionnaire_questions');
	}

}
