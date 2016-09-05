<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnInQuestionnareTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->dropForeign('question_choices_requirement_id_foreign');
            $table->dropColumn('requirement_id');
        });
        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->dropForeign('question_choice_answers_questionnaire_user_id_foreign');
            $table->dropColumn('questionnaire_user_id');
            $table->dropForeign('question_choice_answers_cp_questionnaire_action_id_foreign');
            $table->dropColumn('cp_questionnaire_action_id');
        });
        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->dropForeign('question_free_answers_questionnaire_user_id_foreign');
            $table->dropColumn('questionnaire_user_id');
            $table->dropForeign('question_free_answers_cp_questionnaire_action_id_foreign');
            $table->dropColumn('cp_questionnaire_action_id');
        });
        Schema::dropIfExists('questionnaire_users');
        Schema::table('questionnaire_questions', function(Blueprint $table)
        {
            $table->dropForeign('questionnaire_questions_cp_questionnaire_action_id_foreign');
            $table->dropColumn('cp_questionnaire_action_id');
            $table->dropColumn('requirement_flg');
            $table->dropColumn('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire_questions', function(Blueprint $table)
        {
            $table->integer('cp_questionnaire_action_id')->unsigned()->after('type_id');
            $table->tinyInteger('requirement_flg')->default(0);
            $table->integer('number')->unsigned();
            $table->index('cp_questionnaire_action_id');
        });
        // 外部キーを付けるために値を挿入
        DB::statement('UPDATE questionnaire_questions SET cp_questionnaire_action_id = (SELECT MIN(id) FROM cp_questionnaire_actions);');
        Schema::table('questionnaire_questions', function(Blueprint $table)
        {
            $table->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
        });

        Schema::create('questionnaire_users', function(Blueprint $table)
        {
            // auto increment id (primary key)
            $table->increments('id');

            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_questionnaire_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $table->timestamps();
            $table->index('cp_user_id');
            $table->index('cp_questionnaire_action_id');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
        });
        // デフォルトで値を挿入
        DB::statement('INSERT INTO questionnaire_users(cp_user_id,cp_questionnaire_action_id) VALUES ((SELECT MIN(id) FROM cp_users),(SELECT MIN(id) FROM cp_questionnaire_actions));');

        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->integer('questionnaire_user_id')->unsigned()->after('id');
            $table->integer('cp_questionnaire_action_id')->unsigned()->after('question_id');
            $table->index('questionnaire_user_id');
            $table->index('cp_questionnaire_action_id');
        });
        DB::statement('UPDATE question_free_answers SET questionnaire_user_id = (SELECT MIN(id) FROM questionnaire_users),cp_questionnaire_action_id = (SELECT MIN(id) FROM cp_questionnaire_actions);');
        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->foreign('questionnaire_user_id')->references('id')->on('questionnaire_users');
            $table->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
        });

        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->integer('questionnaire_user_id')->unsigned()->after('id');
            $table->integer('cp_questionnaire_action_id')->unsigned()->after('question_id');
            $table->index('questionnaire_user_id');
            $table->index('cp_questionnaire_action_id');
        });
        DB::statement('UPDATE question_choice_answers SET questionnaire_user_id = (SELECT MIN(id) FROM questionnaire_users),cp_questionnaire_action_id = (SELECT MIN(id) FROM cp_questionnaire_actions);');
        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->foreign('questionnaire_user_id')->references('id')->on('questionnaire_users');
            $table->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
        });

        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->integer('requirement_id')->unsigned()->after('question_id');
            $table->index('requirement_id');
        });
        DB::statement('UPDATE question_choices SET requirement_id = (SELECT MIN(id) FROM question_choice_requirements);');
        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->foreign('requirement_id')->references('id')->on('question_choice_requirements');
        });
    }
}
