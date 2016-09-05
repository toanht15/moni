<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoniplaFreeItemChoiceSyncs extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monipla_free_item_choice_relations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('brandco_free_item')->unsigned();
            $table->string('input_value', 255)->default('');
            $table->integer('choice_id')->unsigned();
            $table->string('choice', 512)->default('');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brandco_free_item')->references('id')->on('profile_questionnaire_questions');
            $table->foreign('choice_id')->references('id')->on('profile_question_choices');
        });

        Schema::table('monipla_free_item_relations', function(Blueprint $table)
        {
            $table->dropForeign('monipla_free_item_relations_brandco_free_item_foreign');
            $table->foreign('brandco_free_item')->references('id')->on('profile_questionnaire_questions');
        });

        Schema::drop('monipla_free_item_syncs');

        Schema::create('monipla_free_item_choice_syncs', function(Blueprint $table)
        {
            // auto increment id (primary key)
            $table->bigIncrements('id');
            $table->integer('choice_id')->unsigned();
            $table->integer('questionnaires_questions_relation_id')->unsigned();
            $table->bigInteger('brands_users_relation_id')->unsigned();
            $table->string('answer_text')->default('');
            $table->dateTime('user_free_item_updated')->default('0000-00-00 00:00:00');
            $table->boolean('del_flg')->default(false);
            // created_at, updated_at DATETIME
            $table->timestamps();
            $table->foreign('choice_id', 'free_item_choice_answers_foreign_key')->references('id')->on('profile_question_choices');
            $table->foreign('questionnaires_questions_relation_id', 'free_item_choice_relation_foreign_key')->references('id')->on('profile_questionnaires_questions_relations');
            $table->foreign('brands_users_relation_id', 'free_item_choice_brands_users_foreign_key')->references('id')->on('brands_users_relations');
        });

        Schema::create('monipla_free_item_free_syncs', function(Blueprint $table)
        {
            // auto increment id (primary key)
            $table->bigIncrements('id');
            $table->integer('questionnaires_questions_relation_id')->unsigned();
            $table->bigInteger('brands_users_relation_id')->unsigned();
            $table->string('answer_text')->default('');
            $table->dateTime('user_free_item_updated')->default('0000-00-00 00:00:00');
            $table->boolean('del_flg')->default(false);
            // created_at, updated_at DATETIME
            $table->timestamps();
            $table->foreign('questionnaires_questions_relation_id', 'free_item_free_relation_foreign_key')->references('id')->on('profile_questionnaires_questions_relations');
            $table->foreign('brands_users_relation_id', 'free_item_free_brands_users_foreign_key')->references('id')->on('brands_users_relations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('monipla_free_item_choice_relations');

        Schema::table('monipla_free_item_relations', function(Blueprint $table)
        {
            $table->dropForeign('monipla_free_item_relations_brandco_free_item_foreign');
            $table->foreign('brandco_free_item')->references('id')->on('profile_questionnaires');
        });

        Schema::create('monipla_free_item_syncs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('relation_id')->default(0);
            $table->unsignedInteger('question_id')->default(0);
            $table->String('answer', 255)->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('relation_id')->references('id')->on('brands_users_relations');
            $table->foreign('question_id')->references('id')->on('profile_questionnaires');
            $table->unique(array('relation_id','question_id'));
        });

        Schema::drop('monipla_free_item_choice_syncs');

        Schema::drop('monipla_free_item_free_syncs');
    }
}
