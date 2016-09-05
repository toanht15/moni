<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSkipTwitterFollow extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_twitter_follow_actions', function(Blueprint $table)
		{
            $table->boolean('skip_flg')->default(1)->after('title');;
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_twitter_follow_actions', function(Blueprint $table)
		{
            $table->dropColumn('skip_flg');
		});
	}

}
