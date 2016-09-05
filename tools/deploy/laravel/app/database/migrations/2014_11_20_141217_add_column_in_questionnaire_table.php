<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInQuestionnaireTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaires_questions_relations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_questionnaire_action_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->tinyInteger('requirement_flg')->default(0);
            $table->integer('number')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_questionnaire_action_id','questionnaires_questions_relations_action_id')->references('id')->on('cp_questionnaire_actions');
            $table->foreign('question_id')->references('id')->on('questionnaire_questions');
        });

        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->integer('question_id')->unsigned()->after('id');
        });

        DB::statement('ALTER TABLE `question_choice_answers` MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE `question_free_answers` MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT');

        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->bigInteger('brands_users_relation_id')->unsigned()->after('id');
            $table->integer('questionnaires_questions_relation_id')->unsigned()->after('id');
        });

        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->bigInteger('brands_users_relation_id')->unsigned()->after('id');
            $table->integer('questionnaires_questions_relation_id')->unsigned()->after('id');
        });

        DB::statement('CREATE INDEX `choice_answers_brands_users_relation_id_question_id_index` ON `question_choice_answers`(`brands_users_relation_id`,`question_id`)');
        DB::statement('CREATE INDEX `free_answers_brands_users_relation_id_question_id_index` ON `question_free_answers`(`brands_users_relation_id`,`question_id`)');

        DB::statement('ALTER TABLE `question_choice_requirements` CHANGE `randam_order_flg` `random_order_flg` tinyint(4) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('questionnaires_questions_relations');

        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->dropColumn('question_id');
        });

        DB::statement('ALTER TABLE `question_choice_answers` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE `question_free_answers` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT');

        Schema::table('question_choice_answers', function(Blueprint $table)
        {
            $table->dropColumn('brands_users_relation_id');
            $table->dropColumn('questionnaires_questions_relation_id');
        });

        Schema::table('question_free_answers', function(Blueprint $table)
        {
            $table->dropColumn('brands_users_relation_id');
            $table->dropColumn('questionnaires_questions_relation_id');
        });

        DB::statement('ALTER TABLE `question_choice_answers` DROP INDEX `choice_answers_brands_users_relation_id_question_id_index`');
        DB::statement('ALTER TABLE `question_free_answers` DROP INDEX `free_answers_brands_users_relation_id_question_id_index`');

        DB::statement('ALTER TABLE `question_choice_requirements` CHANGE `random_order_flg` `randam_order_flg` tinyint(4) NOT NULL DEFAULT 0');
    }
}
