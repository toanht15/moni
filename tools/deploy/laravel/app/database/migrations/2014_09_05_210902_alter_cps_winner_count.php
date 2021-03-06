<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpsWinnerCount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE `cps` MODIFY  `winner_count` tinyint(4) NOT NULL DEFAULT 1');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `cps` MODIFY  `winner_count` tinyint(4) NOT NULL DEFAULT 0');
	}

}
