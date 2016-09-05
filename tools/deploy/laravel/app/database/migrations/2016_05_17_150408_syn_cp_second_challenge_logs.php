<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SynCpSecondChallengeLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('syn_cp_second_challenge_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('syn_cp_challenge_log_id')->unsigned();
			$table->timestamp('menu_clicked_at')->default('0000-00-00 00:00:00');
			$table->timestamp('challenged_at')->default('0000-00-00 00:00:00');
			$table->integer('challenge_mode');
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('syn_cp_challenge_log_id')->references('id')->on('syn_cp_challenge_logs');
			$table->index('menu_clicked_at');
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
		Schema::drop('syn_cp_second_challenge_logs');
	}

}
