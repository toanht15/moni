<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpJoinFinishActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_join_finish_actions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('title', 255)->default('');
            $table->longText('text');
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
        Schema::drop('cp_join_finish_actions');
	}

}
