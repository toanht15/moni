<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpFreeAnswerActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_free_answer_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('question');
            $table->string('button_label')->default('送信する');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cp_free_answer_actions');
	}

}
