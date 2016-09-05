<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpInstagramFollowActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_instagram_follow_actions', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('title', 255)->default('');
            $table->boolean('skip_flg')->default(1);
            $table->boolean('del_flg')->default(0);
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
		Schema::drop('cp_instagram_follow_actions');
	}

}
