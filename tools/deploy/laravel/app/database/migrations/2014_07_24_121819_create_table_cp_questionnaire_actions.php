<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpQuestionnaireActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_questionnaire_actions', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');
            $t->integer('cp_action_id')->unsigned();
            $t->string('image_url', 512)->default('');
            $t->longText('text');
            $t->string('button_label_text')->default('回答する');
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->foreign('cp_action_id')->references('id')->on('cp_actions');
            $t->unique('cp_action_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cp_questionnaire_actions');
    }

}
