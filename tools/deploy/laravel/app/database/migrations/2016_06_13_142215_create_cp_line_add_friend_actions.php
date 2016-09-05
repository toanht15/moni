<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpLineAddFriendActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_line_add_friend_actions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();;
            $table->string('title',512);
            $table->string('line_account_name');
            $table->string('line_account_id');
            $table->text('comment');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cp_line_add_friend_actions');
	}
}
