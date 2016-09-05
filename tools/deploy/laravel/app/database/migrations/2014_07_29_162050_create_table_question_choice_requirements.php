<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionChoiceRequirements extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_choice_requirements', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('question_id')->unsigned();
            $t->tinyInteger('use_other_choice_flg')->default(0);
            $t->tinyInteger('randam_order_flg')->default(0);
            $t->tinyInteger('multi_answer_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('question_id');
            $t->foreign('question_id')->references('id')->on('questionnaire_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('question_choice_requirements');
    }

}
