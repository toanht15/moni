<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreeAnswerActionAnswer extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_free_answer_action_answers', function(Blueprint $t)
        {
            $t->increments('id');

            $t->integer('cp_user_id')->unsigned();
            $t->integer('cp_action_id')->unsigned();
            $t->string('free_answer',2048)->default('');
            $t->tinyInteger('del_flg')->default(0);

            $t->timestamps();
            $t->index('cp_user_id');
            $t->index('cp_action_id');
            $t->foreign('cp_user_id')->references('id')->on('cp_users');
            $t->foreign('cp_action_id')->references('id')->on('cp_actions');
            $t->unique(array('cp_user_id', 'cp_action_id'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cp_free_answer_action_answers');
	}

}
