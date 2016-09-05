<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpNextActionInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_next_action_info', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('next_action_table_id')->unsigned();
            $table->string('label',80)->default('');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('next_action_table_id')->references('id')->on('cp_next_actions');
            $table->unique('next_action_table_id');
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
        Schema::drop('cp_next_action_info');
	}

}
