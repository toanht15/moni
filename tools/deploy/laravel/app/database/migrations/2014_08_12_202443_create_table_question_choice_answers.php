<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionChoiceAnswers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_choice_answers', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('questionnaire_user_id')->unsigned();
            $t->integer('question_id')->unsigned();
            $t->integer('cp_questionnaire_action_id')->unsigned();
            $t->integer('choice_id')->unsigned();
            $t->string('answer_text')->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('questionnaire_user_id');
            $t->index('question_id');
            $t->index('cp_questionnaire_action_id');
            $t->index('choice_id');
            $t->foreign('questionnaire_user_id')->references('id')->on('questionnaire_users');
            $t->foreign('question_id')->references('id')->on('questionnaire_questions');
            $t->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
            $t->foreign('choice_id')->references('id')->on('question_choices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('question_choice_answers');
    }

}
