<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexInQuestionnaireTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->foreign('question_id')->references('id')->on('questionnaire_questions');
        });
        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->foreign('questionnaires_questions_relation_id','choice_answers_questionnaires_questions_relation_foreign')->references('id')->on('questionnaires_questions_relations');
            $table->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
            $table->index(array('questionnaires_questions_relation_id','brands_users_relation_id'),'questionnaires_questions_relations_brands_users_choice_index');
        });
        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->foreign('questionnaires_questions_relation_id','free_answers_questionnaires_questions_relation_foreign')->references('id')->on('questionnaires_questions_relations');
            $table->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
            $table->index(array('questionnaires_questions_relation_id','brands_users_relation_id'),'questionnaires_questions_relations_brands_users_free_index');
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->dropForeign('question_choices_question_id_foreign');
        });
        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->dropForeign('choice_answers_questionnaires_questions_relation_foreign');
            $table->dropForeign('question_choice_answers_brands_users_relation_id_foreign');
            $table->dropIndex('questionnaires_questions_relations_brands_users_choice_index');
        });
        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->dropForeign('free_answers_questionnaires_questions_relation_foreign');
            $table->dropForeign('question_free_answers_brands_users_relation_id_foreign');
            $table->dropIndex('questionnaires_questions_relations_brands_users_free_index');
        });
	}

}
