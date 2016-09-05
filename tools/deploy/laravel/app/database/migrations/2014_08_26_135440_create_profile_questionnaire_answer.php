<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionnaireAnswer extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('profile_questionnaire_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('relation_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->string('answer', 2048)->default('');
            $table->timestamps();
            $table->foreign('relation_id', 'answer_relation_foreign')->references('id')->on('brands_users_relations');
            $table->foreign('question_id', 'answer_question_foreign')->references('id')->on('profile_questionnaires');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('profile_questionnaire_answers');
	}

}
