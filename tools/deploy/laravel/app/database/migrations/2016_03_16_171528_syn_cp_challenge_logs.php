<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SynCpChallengeLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('syn_cp_challenge_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('syn_cp_id')->unsigned();
			$table->timestamp('challenged_at')->default('0000-00-00 00:00:00');
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('syn_cp_id')->references('id')->on('syn_cps');
			$table->index('challenged_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('syn_cp_challenge_logs');
	}

}
