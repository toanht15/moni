<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInstantWinUserLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('instant_win_user_logs', function(Blueprint $table) {
            $table->integer('instant_win_prize_id')->default(0)->after('cp_user_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('instant_win_user_logs', function(Blueprint $table) {
            $table->dropColumn('instant_win_prize_id');
		});
	}

}
