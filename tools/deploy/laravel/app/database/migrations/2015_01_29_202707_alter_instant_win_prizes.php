<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInstantWinPrizes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('instant_win_prizes', function(Blueprint $table)
		{
            $table->integer('max_winner_count')->default(0)->after('cp_instant_win_action_id');
            $table->integer('winner_count')->default(0)->after('max_winner_count');
            $table->decimal('winning_rate', 6, 3)->after('winner_count');
            $table->dateTime('win_time')->after('winning_rate');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('instant_win_prizes', function(Blueprint $table)
		{
            $table->dropColumn('winner_time');
            $table->dropColumn('winning_rate');
            $table->dropColumn('winner_count');
            $table->dropColumn('max_winner_count');
		});
	}

}
