<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionChoices extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_choices', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('requirement_id')->unsigned();
            $t->smallInteger('choice_num')->unsigned();
            $t->string('choice',512)->default('');
            $t->tinyInteger('other_choice_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('requirement_id');
            $t->foreign('requirement_id')->references('id')->on('question_choice_requirements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('question_choices');
    }

}
