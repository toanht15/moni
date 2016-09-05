<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstantWinUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instant_win_users', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->integer('cp_user_id')->unsigned();
            $table->tinyInteger('prize_status')->default(0);
            $table->dateTime('last_join_at');
            $table->unsignedInteger('join_count')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id', 'instant_win_users_cp_action_id_foreign')->references('id')->on('cp_actions');
            $table->foreign('cp_user_id', 'instant_win_users_cp_user_id_foreign')->references('id')->on('cp_users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('instant_win_users');
	}

}
