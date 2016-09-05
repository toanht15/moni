<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryProfileQuestionnaire extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_entry_profile_questionnaires', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_questionnaire_id')->unsigned();
            $table->integer('cp_entry_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('profile_questionnaire_id')->references('id')->on('profile_questionnaire_questions');
            $table->foreign('cp_entry_action_id')->references('id')->on('cp_entry_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry_profile_questionnaires', function(Blueprint $table) {
            $table->drop();
        });
    }

}