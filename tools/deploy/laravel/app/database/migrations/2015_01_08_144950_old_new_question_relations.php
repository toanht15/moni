<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OldNewQuestionRelations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_new_question_relations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('old_question_id')->unsigned();
            $table->integer('new_question_id')->unsigned();
            $table->timestamps();

            $table->foreign('old_question_id')->references('id')->on('profile_questionnaires');
            $table->foreign('new_question_id')->references('id')->on('profile_questionnaire_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('old_new_question_relations');
    }

}
