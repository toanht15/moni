<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionnaireUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_users', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');

            $t->bigInteger('user_id')->unsigned();
            $t->integer('cp_questionnaire_action_id')->unsigned();
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('cp_questionnaire_action_id');
            $t->index('user_id');
            $t->foreign('cp_questionnaire_action_id')->references('id')->on('cp_questionnaire_actions');
            $t->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('questionnaire_users');
    }

}
