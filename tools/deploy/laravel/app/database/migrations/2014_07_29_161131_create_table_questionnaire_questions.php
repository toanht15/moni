<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionnaireQuestions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_questions', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('type_id')->unsigned();
            $t->integer('cp_questionnaire_action_id')->unsigned();
            $t->string('question',1024)->default('');
            $t->tinyInteger('requirement_flg')->default(0);
            $t->integer('number')->unsigned();
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('type_id');
            $t->index('cp_questionnaire_action_id');
            $t->foreign('type_id')->references('id')->on('question_types');
            $t->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('questionnaire_questions');
    }

}
